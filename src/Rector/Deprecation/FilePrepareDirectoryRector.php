<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated file_prepare_directory() calls.
 *
 * See https://www.drupal.org/node/3006851 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class FilePrepareDirectoryRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'file_prepare_directory';

    protected $serviceName = 'file_system';

    protected $serviceMethodName = 'prepareDirectory';


    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated file_prepare_directory() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$result = file_prepare_directory($directory, $options);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$result = \Drupal::service('file_system')->prepareDirectory($directory, $options);
CODE_AFTER
            )
        ]);
    }
}
