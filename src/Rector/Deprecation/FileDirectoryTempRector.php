<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated file_directory_temp() calls.
 *
 * See https://www.drupal.org/node/3039255 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class FileDirectoryTempRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'file_directory_temp';

    protected $serviceName = 'file_system';

    protected $serviceMethodName = 'getTempDirectory';

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated file_directory_temp() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
$dir = file_directory_temp();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$dir = \Drupal::service('file_system')->getTempDirectory();
CODE_AFTER
            )
        ]);
    }
}
