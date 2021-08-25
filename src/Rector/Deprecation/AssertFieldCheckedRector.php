<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertFieldCheckedRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertFieldChecked';
    protected $methodName = 'checkboxChecked';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal\FunctionalTests\AssertLegacyTrait::assertFieldChecked() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertFieldChecked('edit-settings-view-mode', 'default');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->checkboxChecked('edit-settings-view-mode', 'default');
CODE_AFTER
            ),

        ]);
    }

}
