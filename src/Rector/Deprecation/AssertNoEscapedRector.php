<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoEscapedRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertNoEscaped';
    protected $methodName = 'assertNoEscaped';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNoEscaped() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertNoEscaped('<div class="escaped">');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->assertNoEscaped('<div class="escaped">');
CODE_AFTER
            )
        ]);
    }

}
