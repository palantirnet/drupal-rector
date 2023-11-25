<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3401941
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\\Tests\\field\\Traits\\EntityReferenceTestTrait' => 'Drupal\\Tests\\field\\Traits\\EntityReferenceFieldCreationTrait',
    ]);
};
