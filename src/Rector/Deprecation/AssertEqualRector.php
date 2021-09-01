<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertEqualRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertEqual';
    protected $methodName = 'assertEquals';
    protected $isAssertSessionMethod = false;
    protected $declaringSource = 'Drupal\KernelTests\AssertLegacyTrait';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertEqual() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertEqual('Actual', 'Expected', 'Message');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertEquals('Actual', 'Expected', 'Message');
CODE_AFTER
            )
        ]);
    }

}
