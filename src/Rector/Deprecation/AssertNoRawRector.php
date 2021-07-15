<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoRawRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertNoRaw';
    protected $methodName = 'responseNotContains';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNoRaw() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertNoRaw('bartik/logo.svg');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->responseNotContains('bartik/logo.svg');
CODE_AFTER
            )
        ]);
    }

    protected function processArgs(array $args): array
    {
        // We do not pass the full `$node->args` to the new method call, as the
        // legacy assert from Simpletest used to support a message. In fact,
        // assertText dropped that, but many code bases still have a second
        // argument for the message. Let's help them drop it.
        return [$args[0]];
    }

}
