<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class FileCopyRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'file_copy';

    protected $serviceName = 'file.repository';

    protected $serviceMethodName = 'copy';

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_copy() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
file_copy();
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
\Drupal::service('file.repository')->copy();
CODE_AFTER
            )
        ]);
    }
}
