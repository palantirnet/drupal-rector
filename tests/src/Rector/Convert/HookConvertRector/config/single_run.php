<?php

declare(strict_types=1);

use DrupalRector\Rector\Convert\NestedRuleRunner;
use DrupalRector\Rector\Deprecation\FunctionToStaticRector;
use DrupalRector\Rector\ValueObject\FunctionToStaticConfiguration;
use DrupalRector\Tests\Rector\Convert\HookConvertRector\Stub\CapturingHookConvertRector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

/*
 * HookConvertRector next to deprecation rules, wired for a single run.
 *
 * format_size() -> ByteSizeMarkup::create() -> SizeMarkup::create() is a chain:
 * the second rule only applies to the output of the first.
 */
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(CapturingHookConvertRector::class);
    NestedRuleRunner::register($rectorConfig);
    $rectorConfig->ruleWithConfiguration(FunctionToStaticRector::class, [
        new FunctionToStaticConfiguration('10.2.0', 'format_size', 'Drupal\Core\StringTranslation\ByteSizeMarkup', 'create'),
    ]);
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        'Drupal\Core\StringTranslation\ByteSizeMarkup' => 'Drupal\Core\StringTranslation\SizeMarkup',
    ]);
    $rectorConfig->fileExtensions(['module']);
};
