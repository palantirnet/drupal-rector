<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated file_unmanaged_save_data() calls.
 *
 * See https://www.drupal.org/node/3006851 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class FileUnmanagedSaveDataRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'file_unmanaged_save_data';

    protected $serviceName = 'file_system';

    protected $serviceMethodName = 'saveData';


    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_unmanaged_save_data() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$result = file_unmanaged_save_data($data, $destination, $replace);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$result = \Drupal::service('file_system')->saveData($data, $destination, $replace);
CODE_AFTER
            )
        ]);
    }
}
