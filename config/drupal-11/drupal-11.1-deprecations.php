<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\PluginBaseIsConfigurableRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveModuleHandlerDeprecatedMethodsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceLocaleConfigBatchFunctionsRector;
use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3459533
    // PluginBase::isConfigurable() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by instanceof \Drupal\Component\Plugin\ConfigurableInterface.
    $rectorConfig->rule(PluginBaseIsConfigurableRector::class);

    // https://www.drupal.org/node/3151086
    // AliasWhitelist and AliasWhitelistInterface deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by AliasPrefixList and AliasPrefixListInterface.
    // AliasManager::pathAliasWhitelistRebuild() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by pathAliasPrefixListRebuild().
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\path_alias\AliasWhitelist' => 'Drupal\path_alias\AliasPrefixList',
        'Drupal\path_alias\AliasWhitelistInterface' => 'Drupal\path_alias\AliasPrefixListInterface',
        'Drupal\Core\Routing\MatchingRouteNotFoundException' => 'Symfony\Component\Routing\Exception\ResourceNotFoundException',
    ]);
    $rectorConfig->ruleWithConfiguration(MethodToMethodWithCheckRector::class, [
        new MethodToMethodWithCheckConfiguration('Drupal\path_alias\AliasManager', 'pathAliasWhitelistRebuild', 'pathAliasPrefixListRebuild'),
    ]);

    // https://www.drupal.org/node/3442009
    // ModuleHandlerInterface::writeCache() deprecated in drupal:11.1.0, removed in drupal:12.0.0. No replacement needed.
    // ModuleHandlerInterface::getHookInfo() deprecated in drupal:11.1.0, removed in drupal:12.0.0. Replaced by [].
    $rectorConfig->rule(RemoveModuleHandlerDeprecatedMethodsRector::class);

    // https://www.drupal.org/node/3575254
    // locale_config_batch_set_config_langcodes() and locale_config_batch_refresh_name() deprecated
    // in drupal:11.1.0, removed in drupal:12.0.0. Renamed to update_default_config_langcodes
    // and update_config_translations respectively.
    $rectorConfig->rule(ReplaceLocaleConfigBatchFunctionsRector::class);
};
