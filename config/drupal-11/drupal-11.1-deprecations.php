<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\PluginBaseIsConfigurableRector;
use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3459533
    // PluginBase::isConfigurable() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by instanceof \Drupal\Component\Plugin\ConfigurableInterface.
    $rectorConfig->rule(PluginBaseIsConfigurableRector::class);

    // https://www.drupal.org/node/3151086
    // AliasManager::pathAliasWhitelistRebuild() deprecated in drupal:11.1.0, removed in drupal:12.0.0.
    // Replaced by pathAliasPrefixListRebuild().
    $rectorConfig->ruleWithConfiguration(MethodToMethodWithCheckRector::class, [
        new MethodToMethodWithCheckConfiguration('Drupal\path_alias\AliasManager', 'pathAliasWhitelistRebuild', 'pathAliasPrefixListRebuild'),
    ]);
};
