<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertIdenticalRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertIdentical';
    protected $methodName = 'assertSame';
    protected $isAssertSessionMethod = false;
    protected $declaringSource = 'Drupal\KernelTests\AssertLegacyTrait';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertIdentical() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertIdentical('Actual', 'Expected', 'Message');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSame('Actual', 'Expected', 'Message');
CODE_AFTER
            )
        ]);
    }

}
