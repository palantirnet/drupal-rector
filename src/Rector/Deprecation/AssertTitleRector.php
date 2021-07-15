<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertTitleRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertTitle';
    protected $methodName = 'titleEquals';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertTitle() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertTitle('Block layout | Drupal');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->titleEquals('Block layout | Drupal');
CODE_AFTER
            )
        ]);
    }

}
