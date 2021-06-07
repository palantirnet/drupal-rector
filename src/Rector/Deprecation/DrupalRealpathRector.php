<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated drupal_realpath() calls.
 *
 * See https://www.drupal.org/node/2418133 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class DrupalRealpathRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'drupal_realpath';

    protected $serviceName = 'file_system';

    protected $serviceMethodName = 'realpath';


    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated drupal_realpath() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$path = drupal_realpath($path);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$path = \Drupal::service('file_system')
    ->realpath($path);
CODE_AFTER
            )
        ]);
    }
}
