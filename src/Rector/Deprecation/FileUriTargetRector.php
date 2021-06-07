<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated file_uri_target() calls.
 *
 * See https://www.drupal.org/node/3035273 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class FileUriTargetRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'file_uri_target';

    protected $serviceName = 'stream_wrapper_manager';

    protected $serviceMethodName = 'getTarget';


    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_uri_target() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
$result = file_uri_target($uri)
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$result = \Drupal::service('stream_wrapper_manager')->getTarget($uri);
CODE_AFTER
            )
        ]);
    }
}
