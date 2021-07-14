<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertElementNotPresentRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertElementNotPresent';
    protected $methodName = 'elementNotExists';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertElementNotPresent() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertElementNotPresent('css', '.region-content-message.region-empty');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->elementNotExists('css', '.region-content-message.region-empty');
CODE_AFTER
            )
        ]);
    }

}
