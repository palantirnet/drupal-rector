<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\ActionAnnotationToAttributeRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // @see https://www.drupal.org/node/3395575
    $rectorConfig->rule(ActionAnnotationToAttributeRector::class);
};
