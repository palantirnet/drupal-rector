<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoPatternRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertNoPattern';
    protected $methodName = 'responseNotMatches';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNoPattern() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertNoPattern('|<h4[^>]*></h4>|', 'No empty H4 element found.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->responseNotMatches('|<h4[^>]*></h4>|', 'No empty H4 element found.');
CODE_AFTER
            )
        ]);
    }

}
