<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertOptionByTextRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertOptionByText';
    protected $methodName = 'optionExists';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertOptionByText() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertOptionByText('edit-settings-view-mode', 'default');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->optionExists('edit-settings-view-mode', 'default');
CODE_AFTER
            ),

        ]);
    }

}
