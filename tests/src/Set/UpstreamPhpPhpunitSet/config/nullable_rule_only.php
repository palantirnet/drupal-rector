<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;

// Control for the set's PHP version raise: the rule on its own, as a project
// would register it.
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(ExplicitNullableParamTypeRector::class);
};
