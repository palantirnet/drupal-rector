<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNotIdenticalRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertNotIdentical';
    protected $methodName = 'assertNotSame';
    protected $isAssertSessionMethod = false;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal\KernelTests\AssertLegacyTrait::assertNotIdentical() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertNotIdentical('Actual', 'Expected', 'Message');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertNotSame('Actual', 'Expected', 'Message');
CODE_AFTER
            )
        ]);
    }

}
