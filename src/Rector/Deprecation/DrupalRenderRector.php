<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated drupal_render() calls.
 *
 * See https://www.drupal.org/node/2912696 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class DrupalRenderRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'drupal_render';

    protected $serviceName = 'renderer';

    protected $serviceMethodName = 'render';


    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated drupal_render() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$result = drupal_render($elements);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$result = \Drupal::service('renderer')->render($elements);
CODE_AFTER
            )
        ]);
    }
}
