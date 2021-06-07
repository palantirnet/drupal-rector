<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\StaticToFunctionBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated Unicode::strtolower() calls.
 *
 * See https://www.drupal.org/node/2850048 for change record.
 *
 * What is covered:
 * - Static replacement
 */
final class UnicodeStrtolowerRector extends StaticToFunctionBase
{
    protected $deprecatedFullyQualifiedClassName = 'Drupal\Component\Utility\Unicode';

    protected $deprecatedMethodName = 'strtolower';

    protected $functionName = 'mb_strtolower';


    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal\Component\Utility\Unicode::strtolower() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$string = \Drupal\Component\Utility\Unicode::strtolower('example');
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$string = mb_strtolower('example');
CODE_AFTER
            )
        ]);
    }
}
