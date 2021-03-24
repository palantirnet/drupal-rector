<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\StaticToFunctionBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated Unicode::substr() calls.
 *
 * See https://www.drupal.org/node/2850048 for change record.
 *
 * What is covered:
 * - Static replacement
 */
final class UnicodeSubstrRector extends StaticToFunctionBase
{
    protected $deprecatedFullyQualifiedClassName = 'Drupal\Component\Utility\Unicode';

    protected $deprecatedMethodName = 'substr';

    protected $functionName = 'mb_substr';


    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal\Component\Utility\Unicode::substr() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$string = \Drupal\Component\Utility\Unicode::substr('example', 0, 2);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$string = mb_substr('example', 0, 2);
CODE_AFTER
            )
        ]);
    }
}
