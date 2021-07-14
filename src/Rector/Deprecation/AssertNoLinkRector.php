<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoLinkRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertNoLink';
    protected $methodName = 'linkNotExists';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNoLink() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertNoLink('Anonymous comment title');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->linkNotExists('Anonymous comment title');
CODE_AFTER
            )
        ]);
    }

}
