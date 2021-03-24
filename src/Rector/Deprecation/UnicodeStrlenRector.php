<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\StaticToFunctionBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated Unicode::strlen() calls.
 *
 * See https://www.drupal.org/node/2850048 for change record.
 *
 * What is covered:
 * - Static replacement
 */
final class UnicodeStrlenRector extends StaticToFunctionBase
{
    protected $deprecatedFullyQualifiedClassName = 'Drupal\Component\Utility\Unicode';

    protected $deprecatedMethodName = 'strlen';

    protected $functionName = 'mb_strlen';


    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal\Component\Utility\Unicode::strlen() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$length = \Drupal\Component\Utility\Unicode::strlen('example');
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$length = mb_strlen('example');
CODE_AFTER
            )
        ]);
    }
}
