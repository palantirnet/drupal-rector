<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoFieldRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertNoField';
    protected $methodName = 'fieldNotExists';
    protected $comment = 'Change assertion to buttonExists() if checking for a button.';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNoField() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
    $this->assertNoField('files[upload]', 'Found file upload field.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
    $this->assertSession()->fieldNotExists('files[upload]', 'Found file upload field.');
CODE_AFTER
            )
        ]);
    }

}
