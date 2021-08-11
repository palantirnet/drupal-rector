<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertUniqueTextRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertUniqueText';
    protected $methodName = 'pageTextContainsOnce';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertUniqueText() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertUniqueText('Color set');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->pageTextContainsOnce('Color set')
CODE_AFTER
            )
        ]);
    }

}
