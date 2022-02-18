<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class FileMoveRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'file_move';

    protected $serviceName = 'file.repository';

    protected $serviceMethodName = 'move';

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_move() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
file_move();
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
\Drupal::service('file.repository')->move();
CODE_AFTER
            )
        ]);
    }
}
