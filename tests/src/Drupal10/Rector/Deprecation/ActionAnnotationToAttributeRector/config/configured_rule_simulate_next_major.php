<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\ActionAnnotationToAttributeRector;
use DrupalRector\Drupal10\Rector\ValueObject\DrupalIntroducedAndRemovalVersionConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(ActionAnnotationToAttributeRector::class, $rectorConfig, false, [
        new DrupalIntroducedAndRemovalVersionConfiguration('10.2.0', '10.0.0'),
    ]);
};
