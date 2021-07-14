<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertElementPresentRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertElementPresent';
    protected $methodName = 'elementExists';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertElementPresent() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertElementPresent('css', '.region-content-message.region-empty');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->elementExists('css', '.region-content-message.region-empty');
CODE_AFTER
            )
        ]);
    }

}
