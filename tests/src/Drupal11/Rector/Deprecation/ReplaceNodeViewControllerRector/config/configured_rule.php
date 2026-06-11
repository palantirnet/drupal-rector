<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\ReplaceNodeViewControllerRector;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // Only the custom instantiation rule is exercised here. The companion
    // RenameClassRector pass (use / extends / type-hint / ::class rewrites) is
    // a trusted Rector-core rule registered alongside this one in
    // config/drupal-11/drupal-11.4-breaking.php.
    DeprecationBase::addClass(ReplaceNodeViewControllerRector::class, $rectorConfig, false);
};
