<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces \Drupal::classResolver(ViewsConfigUpdater::class) with
 * \Drupal::service(ViewsConfigUpdater::class).
 *
 * In drupal:11.3.0 the ViewsConfigUpdater class was registered as a service.
 * classResolver() returns a fresh instance on each call, so state set via
 * setDeprecationsEnabled(FALSE) was lost across hook invocations. service()
 * returns the singleton container instance that retains state across the
 * request lifecycle. The new call only works on Drupal >= 11.3.0 because
 * the service isn't registered on older versions, so the replacement is
 * BC-wrapped.
 *
 * @see https://www.drupal.org/node/3529274
 * @see https://www.drupal.org/node/3530638
 */
class ViewsConfigUpdaterClassResolverToServiceRector extends AbstractDrupalCoreRector
{
    // TODO PHPSTAN_MESSAGES ViewsConfigUpdaterClassResolverToServiceRector:
    //   PHPStan emits no deprecation for this call. \Drupal::classResolver()
    //   itself is not @deprecated — only its use to resolve
    //   Drupal\views\ViewsConfigUpdater is now considered wrong, and that
    //   constraint is documented only in the change record. No string is
    //   available to add here.

    /** @var DrupalIntroducedVersionConfiguration[] */
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

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)
    {
        if (!$node instanceof StaticCall) {
            return null;
        }

        // Must be \Drupal::something().
        if (!$this->isName($node->class, 'Drupal')) {
            return null;
        }

        // Must be ::classResolver().
        if (!$this->isName($node->name, 'classResolver')) {
            return null;
        }

        if (count($node->args) !== 1) {
            return null;
        }

        $arg = $node->args[0];
        if (!$arg instanceof Arg) {
            return null;
        }

        $value = $arg->value;
        if (!$value instanceof ClassConstFetch) {
            return null;
        }

        if (!$this->isName($value->name, 'class')) {
            return null;
        }

        // The argument must be Drupal\views\ViewsConfigUpdater::class.
        if (!$this->isName($value->class, 'Drupal\views\ViewsConfigUpdater')) {
            return null;
        }

        // Clone before mutating: the base class re-reads $node when building
        // the BC fallback closure, and a direct mutation would propagate the
        // new identifier into both sides of the DeprecationHelper call.
        $new = clone $node;
        $new->name = new Identifier('service');

        return $new;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace \Drupal::classResolver(ViewsConfigUpdater::class) with \Drupal::service(ViewsConfigUpdater::class) since ViewsConfigUpdater is now registered as a service in drupal:11.3.0.',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
$view_config_updater = \Drupal::classResolver(\Drupal\views\ViewsConfigUpdater::class);
CODE_BEFORE,
                    <<<'CODE_AFTER'
$view_config_updater = \Drupal::service(\Drupal\views\ViewsConfigUpdater::class);
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.3.0')]
                ),
            ]
        );
    }
}
