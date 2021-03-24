<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated drupal_render_root() calls.
 *
 * See https://www.drupal.org/node/2912696 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class DrupalRenderRootRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'drupal_render_root';

    protected $serviceName = 'renderer';

    protected $serviceMethodName = 'renderRoot';


    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated drupal_render_root() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$result = drupal_render_root($elements);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$result = \Drupal::service('renderer')->renderRoot($elements);
CODE_AFTER
            )
        ]);
    }
}
