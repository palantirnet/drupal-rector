<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertIdenticalObjectRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertIdenticalObject';
    protected $methodName = 'assertEquals';
    protected $isAssertSessionMethod = false;
    protected $declaringSource = 'Drupal\KernelTests\AssertLegacyTrait';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertIdenticalObject() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertIdenticalObject('Actual', 'Expected', 'Message');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertEquals('Actual', 'Expected', 'Message');
CODE_AFTER
            )
        ]);
    }

}
