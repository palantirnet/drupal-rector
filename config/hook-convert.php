<?php

declare(strict_types=1);

use DrupalRector\Rector\Convert\HookConvertRector;
use DrupalRector\Rector\Convert\NestedRuleRunner;
use Rector\Config\RectorConfig;

/**
 * Converts procedural hook implementations into OOP hook classes, in the SAME
 * run as the other rules.
 *
 * Registers HookConvertRector and wires in the container it needs to run the
 * other active rules over each hook body before the body is copied into the
 * hook class (see NestedRuleRunner for why that is necessary).
 *
 * Load it next to your deprecation set:
 *
 *   ->withSets([DrupalSetList::COMPOSER_BASED, DrupalSetList::HOOK_CONVERT])
 *
 * Registering HookConvertRector with a plain `$rectorConfig->rule()` instead
 * still works, but then it converts the bodies as they are (use two runs).
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(HookConvertRector::class);
    NestedRuleRunner::register($rectorConfig);
};
