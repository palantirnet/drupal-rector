<?php

declare(strict_types=1);

use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use Rector\Config\RectorConfig;
use Rector\Symfony\Set\SymfonyLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        SymfonyLevelSetList::UP_TO_SYMFONY_63,
    ]);

    // https://www.drupal.org/node/2999981
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.2.0', 'format_size', '\Drupal\Core\StringTranslation\ByteSizeMarkup', 'create'),
    ]);
};
