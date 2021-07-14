<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertLinkByHrefRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertLinkByHref';
    protected $methodName = 'linkByHrefExists';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertLinkByHref() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertLinkByHref('user/1/translations');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->linkByHrefExists('user/1/translations');
CODE_AFTER
            )
        ]);
    }

}
