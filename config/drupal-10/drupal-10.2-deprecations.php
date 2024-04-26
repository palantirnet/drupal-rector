<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\VersionedFunctionToServiceRector;
use DrupalRector\Drupal10\Rector\ValueObject\VersionedFunctionToServiceConfiguration;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        SymfonySetList::SYMFONY_64,
    ]);

    // https://www.drupal.org/node/2999981
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.2.0', 'format_size', 'Drupal\Core\StringTranslation\ByteSizeMarkup', 'create'),
    ]);

    // https://www.drupal.org/node/3265963
    $rectorConfig->ruleWithConfiguration(MethodToMethodWithCheckRector::class, [
        new MethodToMethodWithCheckConfiguration('Drupal\system\Plugin\ImageToolkit\GDToolkit', 'getResource', 'getImage'),
        new MethodToMethodWithCheckConfiguration('Drupal\system\Plugin\ImageToolkit\GDToolkit', 'setResource', 'setImage'),
    ]);

    // https://www.drupal.org/node/3358337
    $rectorConfig->ruleWithConfiguration(VersionedFunctionToServiceRector::class, [
        new VersionedFunctionToServiceConfiguration('10.2.0', '_drupal_flush_css_js', 'asset.query_string', 'reset'),
    ]);
};
