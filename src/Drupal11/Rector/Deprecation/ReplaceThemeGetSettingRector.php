<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated theme_get_setting() and _system_default_theme_features().
 *
 * Both are deprecated in drupal:11.3.0 and removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3035289
 */
final class ReplaceThemeGetSettingRector extends AbstractDrupalCoreRector
{
    private const THEME_SETTINGS_PROVIDER = 'Drupal\Core\Extension\ThemeSettingsProvider';

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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated theme_get_setting() and _system_default_theme_features() with ThemeSettingsProvider equivalents',
            [
                new ConfiguredCodeSample(
                    "theme_get_setting('logo.url');",
                    "\\Drupal::service(\\Drupal\\Core\\Extension\\ThemeSettingsProvider::class)->getSetting('logo.url');",
                    [new DrupalIntroducedVersionConfiguration('11.3.0')]
                ),
                new ConfiguredCodeSample(
                    '_system_default_theme_features();',
                    '\\Drupal\\Core\\Extension\\ThemeSettingsProvider::DEFAULT_THEME_FEATURES;',
                    [new DrupalIntroducedVersionConfiguration('11.3.0')]
                ),
            ]
        );
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

        $funcName = $node->name->toString();

        if ($funcName === 'theme_get_setting') {
            $serviceCall = new StaticCall(
                new FullyQualified('Drupal'),
                'service',
                [new Arg(new ClassConstFetch(
                    new FullyQualified(self::THEME_SETTINGS_PROVIDER),
                    'class'
                ))]
            );

            return new MethodCall($serviceCall, 'getSetting', $node->args);
        }

        if ($funcName === '_system_default_theme_features') {
            return new ClassConstFetch(
                new FullyQualified(self::THEME_SETTINGS_PROVIDER),
                'DEFAULT_THEME_FEATURES'
            );
        }

        return null;
    }
}
