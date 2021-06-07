<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated file_scan_directory() calls.
 *
 * See https://www.drupal.org/node/3038437 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection\
 * - Include if statement wrapper to check if our parameter is a directory.
 */
final class FileScanDirectoryRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'file_scan_directory';

    protected $serviceName = 'file_system';

    protected $serviceMethodName = 'scanDirectory';


    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_scan_directory() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$files = file_scan_directory($directory);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$files = \Drupal::service('file_system')->scanDirectory($directory);
CODE_AFTER
            )
        ]);
    }
}
