<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoLinkByHrefRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertNoLinkByHref';
    protected $methodName = 'linkByHrefNotExists';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNoLinkByHref() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertNoLinkByHref('user/2/translations');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->linkByHrefNotExists('user/2/translations');
CODE_AFTER
            )
        ]);
    }

}
