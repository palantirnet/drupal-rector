<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\BlockContentTestBaseStringToArrayRector;
use DrupalRector\Drupal11\Rector\Deprecation\MovePointerToMouseOverRector;
use DrupalRector\Drupal11\Rector\Deprecation\PluginBaseIsConfigurableRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveModuleHandlerDeprecatedMethodsRector;
use DrupalRector\Drupal11\Rector\Deprecation\RemoveUpdaterPostInstallMethodsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceAddCachedDiscoveryMethodCallRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceLocaleConfigBatchFunctionsRector;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3459533
    // https://www.drupal.org/node/2946122 (change record)
    // PluginBase::isConfigurable() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by instanceof \Drupal\Component\Plugin\ConfigurableInterface.
    $rectorConfig->ruleWithConfiguration(PluginBaseIsConfigurableRector::class, [
        new DrupalIntroducedVersionConfiguration('11.1.0'),
    ]);

    // https://www.drupal.org/node/3467559
    // AliasWhitelist and AliasWhitelistInterface deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by AliasPrefixList and AliasPrefixListInterface.
    // AliasManager::pathAliasWhitelistRebuild() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by pathAliasPrefixListRebuild().
    //
    // https://www.drupal.org/node/3462871 (deprecation)
    // https://www.drupal.org/node/3571057 (removal)
    // https://www.drupal.org/node/3462970 (change record)
    // Drupal\Core\Asset\LibraryDiscovery deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Consumer code should type-hint LibraryDiscoveryInterface; the library.discovery
    // service is now backed by LibraryDiscoveryCollector.
    //
    // https://www.drupal.org/node/3573870
    // https://www.drupal.org/node/3384745 (change record)
    // Drupal\user\Entity\EntityPermissionsRouteProviderWithCheck deprecated in
    // drupal:11.1.0, removed in drupal:12.0.0. Use EntityPermissionsRouteProvider
    // instead. The base provider already enforces the `administer permissions`
    // permission requirement, so the convenience access-check the `WithCheck`
    // variant added is dropped. Owners of custom subclasses that re-added the
    // custom check must port any remaining access logic into the route
    // definition. Doctrine annotation-string references (the dominant real-world
    // pattern) are NOT rewritten — RenameClassRector only touches PHP Name nodes
    // (use/extends/implements/::class/typehints), not strings inside annotations.
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\path_alias\AliasWhitelist' => 'Drupal\path_alias\AliasPrefixList',
        'Drupal\path_alias\AliasWhitelistInterface' => 'Drupal\path_alias\AliasPrefixListInterface',
        'Drupal\Core\Routing\MatchingRouteNotFoundException' => 'Symfony\Component\Routing\Exception\ResourceNotFoundException',
        'Drupal\Core\Asset\LibraryDiscovery' => 'Drupal\Core\Asset\LibraryDiscoveryInterface',
        'Drupal\user\Entity\EntityPermissionsRouteProviderWithCheck' => 'Drupal\user\Entity\EntityPermissionsRouteProvider',
    ]);
    $rectorConfig->ruleWithConfiguration(MethodToMethodWithCheckRector::class, [
        new MethodToMethodWithCheckConfiguration('Drupal\path_alias\AliasManager', 'pathAliasWhitelistRebuild', 'pathAliasPrefixListRebuild', '11.1.0'),
    ]);

    // https://www.drupal.org/node/3442009
    // https://www.drupal.org/node/3368812 (change record)
    // ModuleHandlerInterface::writeCache() deprecated in drupal:11.1.0, removed in drupal:12.0.0. No replacement needed.
    // ModuleHandlerInterface::getHookInfo() deprecated in drupal:11.1.0, removed in drupal:12.0.0. Replaced by [].
    $rectorConfig->rule(RemoveModuleHandlerDeprecatedMethodsRector::class);

    // https://www.drupal.org/node/3575254
    // locale_config_batch_set_config_langcodes() and locale_config_batch_refresh_name() deprecated
    // in drupal:11.1.0, removed in drupal:12.0.0. Renamed to update_default_config_langcodes
    // and update_config_translations respectively.
    $rectorConfig->ruleWithConfiguration(ReplaceLocaleConfigBatchFunctionsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.1.0'),
    ]);

    // https://www.drupal.org/node/3417136
    // https://www.drupal.org/node/3461934 (change record)
    // Updater::postInstall() and postInstallTasks() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // The entire install-via-URL flow was eliminated; overrides are dead code.
    $rectorConfig->rule(RemoveUpdaterPostInstallMethodsRector::class);

    // https://www.drupal.org/node/3196937
    // https://www.drupal.org/node/3473739 (change record)
    // BlockContentTestBase::createBlockContentType() $values deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Callers must pass an explicit array such as ['id' => 'basic'] instead of a plain string.
    $rectorConfig->rule(BlockContentTestBaseStringToArrayRector::class);

    // https://www.drupal.org/node/3421202
    // https://www.drupal.org/node/3460567 (change record)
    // movePointerTo() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by getSession()->getDriver()->mouseOver() with an XPath selector.
    $rectorConfig->rule(MovePointerToMouseOverRector::class);

    // https://www.drupal.org/node/3432827
    // https://www.drupal.org/node/3442229 (change record)
    // addMethodCall('addCachedDiscovery', ...) on plugin.cache_clearer deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by the plugin_manager_cache_clear tag approach.
    $rectorConfig->ruleWithConfiguration(ReplaceAddCachedDiscoveryMethodCallRector::class, [
        new DrupalIntroducedVersionConfiguration('11.1.0'),
    ]);

    // https://www.drupal.org/node/3488176
    // drupal_common_theme() removed in drupal:11.1.0.
    // Replaced by \Drupal\Core\Theme\ThemeCommonElements::commonElements().
    // https://www.drupal.org/node/3268441
    // image_filter_keyword() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\Component\Utility\Image::getKeywordOffset().
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('11.1.0', 'drupal_common_theme', 'Drupal\Core\Theme\ThemeCommonElements', 'commonElements'),
        new FunctionToStaticConfiguration('11.1.0', 'image_filter_keyword', 'Drupal\Component\Utility\Image', 'getKeywordOffset'),
    ]);
};
