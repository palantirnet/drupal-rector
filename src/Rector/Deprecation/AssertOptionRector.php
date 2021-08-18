<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertOptionRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertOption';
    protected $methodName = 'optionExists';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertOption() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertOption('edit-settings-view-mode', 'default');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->optionExists('edit-settings-view-mode', 'default');
CODE_AFTER
            ),

        ]);
    }

}
