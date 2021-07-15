<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertEscapedRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertEscaped';
    protected $methodName = 'assertEscaped';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertEscaped() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertEscaped('Demonstrate block regions (<"Cat" & \'Mouse\'>)');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->assertEscaped('Demonstrate block regions (<"Cat" & \'Mouse\'>)');
CODE_AFTER
            )
        ]);
    }

}
