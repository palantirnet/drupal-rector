<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertPatternRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertPattern';
    protected $methodName = 'responseMatches';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertPattern() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertPattern('|<h4[^>]*></h4>|', 'No empty H4 element found.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->responseMatches('|<h4[^>]*></h4>|', 'No empty H4 element found.');
CODE_AFTER
            )
        ]);
    }

}
