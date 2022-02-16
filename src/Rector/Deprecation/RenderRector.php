<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class RenderRector extends FunctionToServiceBase
{

    protected $deprecatedFunctionName = 'render';

    protected $serviceName = 'renderer';

    protected $serviceMethodName = 'render';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated render() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$output = render($build);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$output = \Drupal::service('renderer')->render($build);
CODE_AFTER
            ),
        ]);
    }

}
