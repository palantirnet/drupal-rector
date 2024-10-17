<?php

declare(strict_types=1);

use DrupalRector\Convert\Rector\HookConvertRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
  $rectorConfig->rule(HookConvertRector::class);
  $rectorConfig->bootstrapFiles([
    __DIR__.'/../drupal-phpunit-bootstrap-file.php',
  ]);
};
