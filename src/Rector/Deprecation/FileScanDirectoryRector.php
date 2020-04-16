<?php

namespace DrupalRector\Rector\Deprecation;

use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
     * Replaces deprecated file_scan_directory() calls.
 *
 * See https://www.drupal.org/node/3038437 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class FileScanDirectoryRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'file_scan_directory';

    protected $serviceName = 'file_system';

    protected $serviceMethodName = 'scanDirectory';


    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated file_scan_directory() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
$files = file_scan_directory($directory);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
if (is_dir($directory)) {
  $files = \Drupal::service('file_system')->scanDirectory($directory);
}
CODE_AFTER
            )
        ]);
    }
}
