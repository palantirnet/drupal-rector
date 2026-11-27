<?php

declare(strict_types=1);

use DrupalRector\Set\DrupalSetList;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([DrupalSetList::COMPOSER_BASED]);
};
