<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated system_region_list() and system_default_region() with Theme object methods.
 *
 * Deprecated in drupal:11.4.0 and removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3015812
 * @see https://www.drupal.org/node/3015925
 */
class SystemRegionFunctionsRector extends AbstractDrupalCoreRector
{
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
        return [FuncCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof FuncCall);

        if (!$node->name instanceof Name) {
            return null;
        }

        return match ($node->name->toString()) {
            'system_region_list' => $this->refactorSystemRegionList($node),
            'system_default_region' => $this->refactorSystemDefaultRegion($node),
            default => null,
        };
    }

    private function refactorSystemRegionList(FuncCall $node): ?MethodCall
    {
        if (empty($node->args) || !$node->args[0] instanceof Arg) {
            return null;
        }

        $themeExpr = $node->args[0]->value;
        $method = 'listAllRegions';

        if (isset($node->args[1]) && $node->args[1] instanceof Arg) {
            $showArg = $node->args[1]->value;
            if (
                ($showArg instanceof ConstFetch && in_array($this->getName($showArg), ['REGIONS_VISIBLE', 'visible'], true))
                || ($showArg instanceof String_ && $showArg->value === 'visible')
            ) {
                $method = 'listVisibleRegions';
            }
        }

        return $this->buildThemeChainCall($themeExpr, $method);
    }

    private function refactorSystemDefaultRegion(FuncCall $node): ?MethodCall
    {
        if (empty($node->args) || !$node->args[0] instanceof Arg) {
            return null;
        }

        return $this->buildThemeChainCall($node->args[0]->value, 'getDefaultRegion');
    }

    private function buildThemeChainCall(Expr $themeExpr, string $method): MethodCall
    {
        $serviceCall = new StaticCall(
            new FullyQualified('Drupal'),
            'service',
            [new Arg(new String_('theme_handler'))]
        );
        $getTheme = new MethodCall($serviceCall, 'getTheme', [new Arg($themeExpr)]);

        return new MethodCall($getTheme, $method, []);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated system_region_list() and system_default_region() with Theme object methods via the theme_handler service.',
            [
                new ConfiguredCodeSample(
                    'system_region_list($theme);',
                    "\Drupal::service('theme_handler')->getTheme(\$theme)->listAllRegions();",
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    'system_region_list($theme, REGIONS_VISIBLE);',
                    "\Drupal::service('theme_handler')->getTheme(\$theme)->listVisibleRegions();",
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    'system_default_region($theme);',
                    "\Drupal::service('theme_handler')->getTheme(\$theme)->getDefaultRegion();",
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }
}
