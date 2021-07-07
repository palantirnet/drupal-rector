<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertResponseRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertResponse';
    protected $methodName = 'statusCodeEquals';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertResponse() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertResponse(200);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->statusCodeEquals(200);
CODE_AFTER
            )
        ]);
    }

}
