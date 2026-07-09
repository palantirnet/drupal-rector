<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitor;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes calls to AliasManager::setCacheKey() and AliasManager::writeCache().
 *
 * Both methods are deprecated in drupal:11.3.0 and removed in drupal:13.0.0
 * with no replacement. They only became no-ops in 11.3.0, when the path alias
 * preload cache was replaced by a Fiber-based bulk-lookup strategy; before
 * 11.3.0 they performed real caching work. Removing the call outright is
 * therefore only safe when the code no longer supports Drupal < 11.3.
 *
 * When backwards compatibility is enabled the call is wrapped in a
 * DeprecationHelper::backwardsCompatibleCall() with a no-op current callable,
 * so the caching still runs on Drupal < 11.3 and is skipped on 11.3+. When
 * backwards compatibility is disabled the call is removed.
 *
 * @see https://www.drupal.org/node/3496369
 * @see https://www.drupal.org/node/3532412
 */
final class RemoveAliasManagerCacheMethodCallsRector extends AbstractDrupalCoreRector
{
    public const PHPSTAN_MESSAGES = [
        'Call to deprecated method setCacheKey() of class Drupal\path_alias\AliasManager. Deprecated in drupal:11.3.0 and is removed from drupal:13.0.0. There is no replacement.',
        'Call to deprecated method writeCache() of class Drupal\path_alias\AliasManager. Deprecated in drupal:11.3.0 and is removed from drupal:13.0.0. There is no replacement.',
    ];

    private const TARGET_METHODS = ['setCacheKey', 'writeCache'];

    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration = [];

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove calls to AliasManager::setCacheKey() and AliasManager::writeCache(), deprecated in drupal:11.3.0 and removed in drupal:13.0.0 with no replacement.',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
$alias = $this->aliasManager->getAliasByPath($path);
$this->aliasManager->setCacheKey($path);
CODE_BEFORE,
                    <<<'CODE_AFTER'
$alias = $this->aliasManager->getAliasByPath($path);
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.3.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Expression::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): int|Node|null
    {
        assert($node instanceof Expression);
        if (!$node->expr instanceof MethodCall) {
            return null;
        }

        $methodCall = $node->expr;
        if (!$this->isNames($methodCall->name, self::TARGET_METHODS)) {
            return null;
        }

        if (
            !$this->isObjectType($methodCall->var, new ObjectType('Drupal\path_alias\AliasManager'))
            && !$this->isObjectType($methodCall->var, new ObjectType('Drupal\path_alias\AliasManagerInterface'))
        ) {
            return null;
        }

        // The shared createBcCallOnExpr() helper cannot be reached through the
        // parent's Expr → Expr path here: the matched node is a whole statement
        // and the "current" (11.3+) behaviour is to do nothing, which has no
        // replacement expression. Build the wrapper ourselves, using a no-op
        // current callable and the original call as the deprecated callable.
        if ($this->supportBackwardsCompatibility($configuration)) {
            $noOp = new ConstFetch(new Name('null'));

            return new Expression($this->createBcCallOnExpr($methodCall, $noOp, $configuration));
        }

        return NodeVisitor::REMOVE_NODE;
    }
}
