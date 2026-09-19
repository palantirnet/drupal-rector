<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3595652
    // https://www.drupal.org/node/3595653 (change record)
    // module_set_weight() and module_config_sort() from module.inc deprecated in
    // drupal:11.5.0, removed in drupal:13.0.0. Replaced by the new
    // \Drupal\Core\Extension\ModuleWeight service (set() and sort()). BC-wrapped
    // because the service does not exist on Drupal < 11.5.
    // TODO PHPSTAN_MESSAGES module_set_weight/module_config_sort:
    //   Not yet captured. Both are @deprecated PHP symbols, so PHPStan emits
    //   "Call to deprecated function module_set_weight()" / "…module_config_sort()",
    //   but the deprecation is not in the installed 11.4-dev test core yet.
    //   Capture the two messages once the test core is updated past core commit
    //   "task: #3595652".
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.5.0', 'module_set_weight', 'Drupal\Core\Extension\ModuleWeight', 'set', true),
        new FunctionToServiceConfiguration('11.5.0', 'module_config_sort', 'Drupal\Core\Extension\ModuleWeight', 'sort', true),
    ]);
};
