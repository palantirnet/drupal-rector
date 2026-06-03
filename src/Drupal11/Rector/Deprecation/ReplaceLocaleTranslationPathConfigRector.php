<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated locale.settings:translation.path config reads with Settings::get().
 *
 * The configuration key locale.settings:translation.path was deprecated in
 * drupal:11.4.0 and is removed in drupal:13.0.0. Site owners who need to
 * customize the interface translations directory path must now set
 * $settings['locale_translation_path'] in settings.php.
 *
 * The transformation is BC-wrapped via DeprecationHelper, so the rewritten
 * code still runs on pre-11.4 Drupal — but be aware of the semantic gap:
 * on Drupal < 11.4 the customised path lives in CONFIG; on Drupal >= 11.4
 * it must live in settings.php. The wrapper switches branches on Drupal
 * version, not on where the value is stored. Before running this rule,
 * confirm that any customised translation path has been moved to
 * $settings['locale_translation_path'] in settings.php; otherwise the new
 * branch will silently fall back to the default 'public://translations'
 * even when the config still holds the customised value.
 *
 * @see https://www.drupal.org/node/3571593
 * @see https://www.drupal.org/node/3571594
 */
class ReplaceLocaleTranslationPathConfigRector extends AbstractDrupalCoreRector
{
    // TODO PHPSTAN_MESSAGES ReplaceLocaleTranslationPathConfigRector:
    //   PHPStan cannot detect this deprecation. The deprecated symbol is the
    //   config KEY 'locale.settings:translation.path' — neither the `get()`
    //   method nor the key string carries a PHP-level @deprecated annotation,
    //   and Drupal core does not trigger_error() on access. The deprecation
    //   notice is emitted only by locale.post_update.php after update.

    private const CONFIG_ACCESSOR_METHODS = ['config', 'get', 'getEditable'];

    private const SETTINGS_CLASS = 'Drupal\\Core\\Site\\Settings';

    private const SETTINGS_KEY = 'locale_translation_path';

    private const SETTINGS_DEFAULT = 'public://translations';

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
            "Replace deprecated \\Drupal::config('locale.settings')->get('translation.path') with Settings::get('locale_translation_path')",
            [
                new ConfiguredCodeSample(
                    "\\Drupal::config('locale.settings')->get('translation.path');",
                    "\\Drupal\\Core\\Site\\Settings::get('locale_translation_path', 'public://translations');",
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof MethodCall);
        if (!$this->isName($node->name, 'get')) {
            return null;
        }
        if (count($node->args) < 1) {
            return null;
        }
        $firstArg = $node->args[0];
        if (!$firstArg instanceof Arg) {
            return null;
        }
        if (!$firstArg->value instanceof String_) {
            return null;
        }
        if ($firstArg->value->value !== 'translation.path') {
            return null;
        }
        if (!$this->isLocaleSettingsConfigReceiver($node->var)) {
            return null;
        }

        return new StaticCall(
            new FullyQualified(self::SETTINGS_CLASS),
            new Identifier('get'),
            [
                new Arg(new String_(self::SETTINGS_KEY)),
                new Arg(new String_(self::SETTINGS_DEFAULT)),
            ]
        );
    }

    /**
     * Walks a chained config-accessor expression to check whether it ultimately
     * targets the locale.settings configuration object.
     *
     * Matches forms like:
     *   \Drupal::config('locale.settings')
     *   \Drupal::configFactory()->get('locale.settings')
     *   $this->config('locale.settings')
     *   $this->configFactory->get('locale.settings')
     */
    private function isLocaleSettingsConfigReceiver(Node $receiver): bool
    {
        $current = $receiver;
        while ($current instanceof MethodCall) {
            if ($this->isNames($current->name, self::CONFIG_ACCESSOR_METHODS)) {
                if (!empty($current->args) && $current->args[0] instanceof Arg) {
                    $arg = $current->args[0]->value;
                    if ($arg instanceof String_ && $arg->value === 'locale.settings') {
                        return true;
                    }
                }
            }
            $current = $current->var;
        }
        if ($current instanceof StaticCall) {
            if ($this->isName($current->name, 'config') && !empty($current->args)) {
                $arg = $current->args[0];
                if ($arg instanceof Arg && $arg->value instanceof String_) {
                    return $arg->value->value === 'locale.settings';
                }
            }
        }

        return false;
    }
}
