<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assert';
    protected $methodName = 'assertTrue';
    protected $isAssertSessionMethod = false;
    protected $declaringSource = 'Drupal\KernelTests\AssertLegacyTrait';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assert() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assert($foo);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertTrue($foo);
CODE_AFTER
            )
        ]);
    }

}
