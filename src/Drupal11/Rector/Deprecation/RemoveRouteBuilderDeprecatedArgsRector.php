<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes the deprecated $module_handler and $controller_resolver arguments
 * from RouteBuilder::__construct().
 *
 * The 6-argument form is rewritten to the new 4-argument form introduced in
 * Drupal 11.4.0. The $module_handler (arg 3) and $controller_resolver (arg 4)
 * parameters were deprecated and removed; $check_provider shifts from position
 * 5 to position 3. YAML route discovery moved to the new YamlRouteDiscovery
 * service.
 *
 * Only rewrites calls with exactly 6 positional arguments. Named arguments or
 * calls with a different argument count are left untouched. Does not handle
 * parent::__construct() calls inside subclasses of RouteBuilder, which would
 * require walking up to the enclosing class to verify the parent chain.
 *
 * @see https://www.drupal.org/node/3311365
 * @see https://www.drupal.org/node/3324751
 */
class RemoveRouteBuilderDeprecatedArgsRector extends AbstractDrupalCoreRector
{
    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    // TODO PHPSTAN_MESSAGES RemoveRouteBuilderDeprecatedArgsRector:
    // RouteBuilder::__construct() is not annotated @deprecated — only passing
    // the (now removed) $module_handler/$controller_resolver arguments triggers
    // a runtime trigger_error in core, keyed on the argument *count* and the
    // $check_provider argument being a ModuleHandlerInterface. PHPStan emits no
    // static deprecation message for this call shape, so upgrade_status cannot
    // match this rector via the standard $rector_covered lookup. Verified
    // against the drupal-core 11.4 RouteBuilder constructor.
    public const PHPSTAN_MESSAGES = [];

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
            'Remove deprecated $module_handler and $controller_resolver arguments from RouteBuilder::__construct()',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
new \Drupal\Core\Routing\RouteBuilder($dumper, $lock, $dispatcher, $moduleHandler, $controllerResolver, $checkProvider);
CODE_BEFORE,
                    <<<'CODE_AFTER'
new \Drupal\Core\Routing\RouteBuilder($dumper, $lock, $dispatcher, $checkProvider);
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof New_);
        if (!$node->class instanceof Name) {
            return null;
        }
        if (!$this->isName($node->class, 'Drupal\Core\Routing\RouteBuilder')) {
            return null;
        }
        if (count($node->args) !== 6) {
            return null;
        }

        // Old signature: ($dumper, $lock, $dispatcher, $module_handler, $controller_resolver, $check_provider)
        // New signature: ($dumper, $lock, $dispatcher, $check_provider)
        // Clone the node and reassign args on the clone so the original node is
        // left intact for the backwards-compatible call's "old" branch.
        $cloned = clone $node;
        $cloned->args = [$node->args[0], $node->args[1], $node->args[2], $node->args[5]];

        return $cloned;
    }
}
