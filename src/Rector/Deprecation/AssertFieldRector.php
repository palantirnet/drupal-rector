<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertFieldRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertField';
    protected $methodName = 'fieldExists';
    protected $comment = 'Change assertion to buttonExists() if checking for a button.';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertField() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
    $this->assertField('files[upload]', 'Found file upload field.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
    $this->assertSession()->fieldExists('files[upload]', 'Found file upload field.');
CODE_AFTER
            )
        ]);
    }

}
