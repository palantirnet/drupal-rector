<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoOptionRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertNoOption';
    protected $methodName = 'optionNotExists';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNoOption() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertNoOption('edit-settings-view-mode', 'default');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->optionNotExists('edit-settings-view-mode', 'default');
CODE_AFTER
            ),

        ]);
    }

}
