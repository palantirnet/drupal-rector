<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
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

    public function getNodeTypes(): array
    {
        return [PropertyFetch::class, NullsafePropertyFetch::class, Assign::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        // Step 1a: $entity->original → $entity->getOriginal()
        // Skip when used as LHS of an Assign — the Assign handler below manages that
        // so BC wrapping captures the original assignment expression correctly.
        // (skip $this->original — non-entity classes have a legitimate $original property)
        if ($node instanceof PropertyFetch) {
            if ($this->isName($node->name, 'original')
                && !$this->isThisVar($node->var)
                && $this->isObjectType($node->var, new ObjectType('Drupal\Core\Entity\EntityInterface'))
            ) {
                if ($node->getAttribute(AttributeKey::IS_BEING_ASSIGNED)) {
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
                return new NullsafeMethodCall($node->var, 'getOriginal');
            }

            return null;
        }

        assert($node instanceof Assign);

        // Step 2: $entity->original = $x → $entity->setOriginal($x)
        // Detect the original PropertyFetch form directly (before any inner transformation)
        // so the BC "old callable" captures the original assignment correctly.
        if ($node->var instanceof PropertyFetch
            && $this->isName($node->var->name, 'original')
            && !$this->isThisVar($node->var->var)
            && $this->isObjectType($node->var->var, new ObjectType('Drupal\Core\Entity\EntityInterface'))
        ) {
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
        ]);
    }
}
