<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\ValueObject\RenameClassRectorConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    DeprecationBase::addClass(DrupalRector\Drupal10\Rector\Deprecation\RenameClassRector::class, $rectorConfig, false, [
        new RenameClassRectorConfiguration('10.1.0', 'Drupal\\Tests\\field\\Traits\\EntityReferenceTestTrait', 'Drupal\\Tests\\field\\Traits\\EntityReferenceFieldCreationTrait'),
        new RenameClassRectorConfiguration('10.1.0', 'Drupal\\OldClass', 'Drupal\\NewClass'),
    ]);
};
