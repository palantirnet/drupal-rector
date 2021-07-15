<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertLinkRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertLink';
    protected $methodName = 'linkExists';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertLink() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertLink('Anonymous comment title');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->linkExists('Anonymous comment title');
CODE_AFTER
            )
        ]);
    }

}
