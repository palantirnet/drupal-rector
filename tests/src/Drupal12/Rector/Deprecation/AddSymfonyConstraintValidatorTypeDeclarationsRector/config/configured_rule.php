<?php

declare(strict_types=1);

use DrupalRector\Drupal12\Rector\Deprecation\AddSymfonyConstraintValidatorTypeDeclarationsRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(AddSymfonyConstraintValidatorTypeDeclarationsRector::class, $rectorConfig, false);
};
