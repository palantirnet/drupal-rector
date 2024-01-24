<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\Deprecation\MethodToMethodWithCheckRector;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\PropertyFetch\RenamePropertyRector;
use Rector\Renaming\ValueObject\RenameProperty;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/2999981
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.2.0', 'format_size', '\Drupal\Core\StringTranslation\ByteSizeMarkup', 'create'),
    ]);

    // https://www.drupal.org/node/3265963
    $rectorConfig->ruleWithConfiguration(MethodToMethodWithCheckRector::class, [
        new MethodToMethodWithCheckConfiguration('Drupal\system\Plugin\ImageToolkit\GDToolkit', 'getResource', 'getImage'),
        new MethodToMethodWithCheckConfiguration('Drupal\system\Plugin\ImageToolkit\GDToolkit', 'setResource', 'setImage'),
    ]);

    // https://www.drupal.org/node/3265963
    $rectorConfig->ruleWithConfiguration(RenamePropertyRector::class, [
        new RenameProperty('Drupal\system\Plugin\ImageToolkit\GDToolkit', 'resource', 'image'),
    ]);
};
