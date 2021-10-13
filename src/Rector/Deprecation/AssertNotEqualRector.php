<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNotEqualRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertNotEqual';
    protected $methodName = 'assertNotEquals';
    protected $isAssertSessionMethod = false;
    protected $declaringSource = 'Drupal\KernelTests\AssertLegacyTrait';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNotEqual() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertNotEqual('Actual', 'Expected', 'Message');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertNotEquals('Actual', 'Expected', 'Message');
CODE_AFTER
            )
        ]);
    }

}
