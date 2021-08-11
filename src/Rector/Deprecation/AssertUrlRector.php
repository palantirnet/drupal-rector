<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertUrlRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertUrl';
    protected $methodName = 'addressEquals';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertUrl() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertUrl('myrootuser');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->addressEquals('myrootuser');
CODE_AFTER
            )
        ]);
    }

}
