<?php

declare(strict_types=1);

use DrupalRector\Drupal10\Rector\Deprecation\VersionedClassConstantToClassConstantRector;
use DrupalRector\Drupal10\Rector\ValueObject\VersionedClassConstantToClassConstantConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(VersionedClassConstantToClassConstantRector::class, [
        new VersionedClassConstantToClassConstantConfiguration(
            '10.3.0',
            'Drupal\Core\File\FileSystemInterface',
            'EXISTS_REPLACE',
            'Drupal\Core\File\FileExists',
            'Replace',
        ),
    ]);
};
