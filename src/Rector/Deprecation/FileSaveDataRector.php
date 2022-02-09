<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class FileSaveDataRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'file_save_data';

    protected $serviceName = 'file.repository';

    protected $serviceMethodName = 'writeData';

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_save_data() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
file_save_data($data);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
\Drupal::service('file.repository')->writeData($data);
CODE_AFTER
            )
        ]);
    }
}
