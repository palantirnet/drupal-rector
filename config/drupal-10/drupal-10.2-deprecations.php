<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // @see https://www.drupal.org/node/3395575
    $rectorConfig->rule(\DrupalRector\Rector\Deprecation\ActionAnnotationToAttributeRector::class);
};
