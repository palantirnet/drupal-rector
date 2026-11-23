<?php

declare(strict_types=1);

use DrupalRector\Rector\Convert\NestedRuleRunner;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\CapturingHookConvertRector;
use Rector\Config\RectorConfig;

/*
 * Single run with importNames(): the nested rules and the name importing
 * must both leave the hook class with correct imports.
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(CapturingHookConvertRector::class);
    NestedRuleRunner::register($rectorConfig);
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.2.0', 'format_size', 'Drupal\Core\StringTranslation\SizeMarkup', 'create'),
    ]);
    $rectorConfig->fileExtensions(['module']);
    $rectorConfig->importNames(true, false);
};
