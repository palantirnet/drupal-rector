<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoFieldCheckedRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertNoFieldChecked';
    protected $methodName = 'checkboxNotChecked';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNoFieldChecked() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertNoFieldChecked('edit-settings-view-mode', 'default');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->checkboxNotChecked('edit-settings-view-mode', 'default');
CODE_AFTER
            ),

        ]);
    }

}
