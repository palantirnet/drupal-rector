<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Unset_;
use PHPStan\Type\ObjectType;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated $entity->original magic property with getOriginal()/setOriginal().
 *
 * Deprecated in drupal:11.2.0, removed in drupal:12.0.0.
 * Skips $this->original to avoid false positives on non-entity classes.
 *
 * @see https://www.drupal.org/node/3295826
 */
final class ReplaceEntityOriginalPropertyRector extends AbstractDrupalCoreRector
{
    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }
        parent::configure($configuration);
    }

    /**
     * Attribute flag marking a $entity->original fetch that sits in the
     * variable position of isset()/unset() and must not be rewritten.
     */
    private const SKIP_IN_ISSET_UNSET = 'drupal_rector_skip_original_in_isset_unset';

    public function getNodeTypes(): array
    {
        // Isset_/Unset_ are visited before their children (pre-order) so Steps
        // 0a/0b can rewrite or tag the ->original operands before the leaf
        // PropertyFetch handlers would otherwise reach them.
        return [Isset_::class, Unset_::class, PropertyFetch::class, NullsafePropertyFetch::class, Assign::class];
    }

    /**
     * @return Node|Node[]|null
     */
    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)
    {
        // Step 0a: isset($entity->original) → $entity->getOriginal() !== NULL.
        // Core's EntityBase::__isset('original') returns getOriginal(), so this
        // is the faithful equivalent. Isset_ is an Expr, so the parent wraps the
        // result in DeprecationHelper automatically, with the original isset() as
        // the deprecated callable. Only the single, direct form has a clean
        // equivalent; nested or multi-operand forms are left in place (with their
        // inner ->original fetches tagged so they are not rewritten into a fatal).
        if ($node instanceof Isset_) {
            if (count($node->vars) === 1 && $this->isEntityOriginalFetch($node->vars[0])) {
                $fetch = $node->vars[0];
                assert($fetch instanceof PropertyFetch);

                return new NotIdentical(new MethodCall($fetch->var, 'getOriginal'), $this->createNull());
            }

            foreach ($node->vars as $var) {
                $this->tagOriginalFetchesInVariableChain($var);
            }

            return null;
        }

        // Step 0b: unset($entity->original) → $entity->setOriginal(NULL).
        // Core's EntityBase::__unset('original') calls setOriginal(NULL). unset()
        // is a statement (not an expression), so it cannot be auto-wrapped by the
        // parent and the deprecated callable cannot itself be unset(); we build
        // the BC wrapper here with `$entity->original = NULL` as the < 11.2 path.
        if ($node instanceof Unset_) {
            return $this->refactorUnset($node, $configuration);
        }

        // Step 1a: $entity->original → $entity->getOriginal()
        // Skip when used as LHS of an Assign — the Assign handler below manages that
        // so BC wrapping captures the original assignment expression correctly.
        // (skip $this->original — non-entity classes have a legitimate $original property)
        if ($node instanceof PropertyFetch) {
            if ($this->isEntityOriginalFetch($node)) {
                if ($node->getAttribute(AttributeKey::IS_BEING_ASSIGNED)) {
                    return null;
                }

                // Direct operand of isset()/unset() (Steps 0a/0b own those) or a
                // nested ->original fetch tagged by them — leave it in place.
                if ($this->isInIssetOrUnsetContext($node)) {
                    return null;
                }

                return new MethodCall($node->var, 'getOriginal');
            }

            return null;
        }

        // Step 1b: $entity?->original → $entity?->getOriginal()
        if ($node instanceof NullsafePropertyFetch) {
            if ($this->isName($node->name, 'original')
                && !$this->isThisVar($node->var)
                && $this->isObjectType($node->var, new ObjectType('Drupal\Core\Entity\EntityInterface'))
            ) {
                if ($this->isInIssetOrUnsetContext($node)) {
                    return null;
                }

                return new NullsafeMethodCall($node->var, 'getOriginal');
            }

            return null;
        }

        assert($node instanceof Assign);

        // Step 2: $entity->original = $x → $entity->setOriginal($x)
        // Detect the original PropertyFetch form directly (before any inner transformation)
        // so the BC "old callable" captures the original assignment correctly.
        if ($this->isEntityOriginalFetch($node->var)) {
            assert($node->var instanceof PropertyFetch);

            return new MethodCall(
                $node->var->var,
                'setOriginal',
                [new Arg($node->expr)]
            );
        }

        return null;
    }

    private function isThisVar(Node $node): bool
    {
        return $node instanceof Variable && $node->name === 'this';
    }

    /**
     * Whether $node is a `$entity->original` fetch on an EntityInterface.
     *
     * Excludes `$this->original`: non-entity classes (e.g. EntityTypeEvent)
     * have a legitimate $original property.
     */
    private function isEntityOriginalFetch(Node $node): bool
    {
        return $node instanceof PropertyFetch
            && $this->isName($node->name, 'original')
            && !$this->isThisVar($node->var)
            && $this->isObjectType($node->var, new ObjectType('Drupal\Core\Entity\EntityInterface'));
    }

    /**
     * Whether this ->original fetch must be left alone because it lives in an
     * isset()/unset() variable position.
     *
     * Rector flags the *direct* operands of isset()/unset() with IS_ISSET_VAR /
     * IS_UNSET_VAR (see ContextNodeVisitor). Those are owned by Steps 0a/0b.
     * Operands nested deeper in the chain — e.g. the inner `$entity->original`
     * of `isset($entity->original->field)` — are not flagged, so Steps 0a/0b tag
     * them with SKIP_IN_ISSET_UNSET instead. Either way, rewriting them to a
     * method call would be a fatal in that context.
     */
    private function isInIssetOrUnsetContext(Node $node): bool
    {
        return $node->getAttribute(AttributeKey::IS_ISSET_VAR) === true
            || $node->getAttribute(AttributeKey::IS_UNSET_VAR) === true
            || $node->getAttribute(self::SKIP_IN_ISSET_UNSET) === true;
    }

    /**
     * @return Node\Stmt[]|null statements replacing the unset(), or null when nothing matched
     */
    private function refactorUnset(Unset_ $unset, VersionedConfigurationInterface $configuration): ?array
    {
        $remaining = [];
        $statements = [];

        foreach ($unset->vars as $var) {
            if ($this->isEntityOriginalFetch($var)) {
                assert($var instanceof PropertyFetch);
                $statements[] = new Expression($this->createSetOriginalNull($var, $configuration));

                continue;
            }

            // Keep this operand in a residual unset() and make sure a nested
            // ->original fetch within it is not rewritten into a fatal.
            $this->tagOriginalFetchesInVariableChain($var);
            $remaining[] = $var;
        }

        if ($statements === []) {
            return null;
        }

        if ($remaining !== []) {
            $unset->vars = $remaining;
            array_unshift($statements, $unset);
        } else {
            // The original unset() statement is gone; carry its leading comments
            // (e.g. a preceding docblock) onto the first replacement statement.
            $statements[0]->setAttribute(AttributeKey::COMMENTS, $unset->getAttribute(AttributeKey::COMMENTS));
        }

        return $statements;
    }

    /**
     * Builds the `$entity->setOriginal(NULL)` replacement for unset(), wrapped in
     * DeprecationHelper when backward compatibility is enabled.
     */
    private function createSetOriginalNull(PropertyFetch $fetch, VersionedConfigurationInterface $configuration): Expr
    {
        $newCall = new MethodCall($fetch->var, 'setOriginal', [new Arg($this->createNull())]);

        if (!$this->supportBackwardsCompatibility($configuration)) {
            return $newCall;
        }

        // The < 11.2 path: `$entity->original = NULL`, the expression form of
        // unsetting the magic property (unset() itself cannot be an arrow body).
        $deprecatedAssign = new Assign(clone $fetch, $this->createNull());

        return $this->createBcCallOnExpr($deprecatedAssign, $newCall, $configuration->getIntroducedVersion());
    }

    private function createNull(): ConstFetch
    {
        return new ConstFetch(new Name('NULL'));
    }

    /**
     * Tags every $entity->original fetch sitting in the variable chain of an
     * isset()/unset() operand so the PropertyFetch handlers leave it in place.
     *
     * isset()/unset() accept only a variable: a base variable followed by
     * property/array-access links. We descend that chain (->var only) so nested
     * forms like isset($entity->original->field) are covered, while a fetch used
     * as an array key — the dim of an ArrayDimFetch — is a normal expression slot
     * and is intentionally left to be rewritten.
     */
    private function tagOriginalFetchesInVariableChain(Node $node): void
    {
        $current = $node;
        while ($current instanceof PropertyFetch || $current instanceof NullsafePropertyFetch || $current instanceof ArrayDimFetch) {
            if (($current instanceof PropertyFetch || $current instanceof NullsafePropertyFetch)
                && $this->isName($current->name, 'original')
            ) {
                $current->setAttribute(self::SKIP_IN_ISSET_UNSET, true);
            }

            $current = $current->var;
        }
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated $entity->original magic property with getOriginal()/setOriginal() method calls (drupal:11.2.0)', [
            new ConfiguredCodeSample(
                '$original = $entity->original;',
                '$original = $entity->getOriginal();',
                [new DrupalIntroducedVersionConfiguration('11.2.0')]
            ),
            new ConfiguredCodeSample(
                '$entity->original = $unchanged;',
                '$entity->setOriginal($unchanged);',
                [new DrupalIntroducedVersionConfiguration('11.2.0')]
            ),
            new ConfiguredCodeSample(
                'if (isset($entity->original)) {}',
                'if ($entity->getOriginal() !== NULL) {}',
                [new DrupalIntroducedVersionConfiguration('11.2.0')]
            ),
            new ConfiguredCodeSample(
                'unset($entity->original);',
                '$entity->setOriginal(NULL);',
                [new DrupalIntroducedVersionConfiguration('11.2.0')]
            ),
        ]);
    }
}
