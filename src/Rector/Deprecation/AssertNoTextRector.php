<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoTextRector extends AssertLegacyTraitBase
{

    // @codingStandardsIgnoreLine
    protected $comment = 'Verify the assertion: pageTextNotContains() for HTML responses, responseNotContains() for non-HTML responses.' .
        PHP_EOL . '// The passed text should be HTML decoded, exactly as a human sees it in the browser.';
    protected $deprecatedMethodName = 'assertNoText';
    protected $methodName = 'pageTextNotContains';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::assertNoText() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->drupalGet('test-page');
$this->assertNoText('Expected content', 'Page has expected content.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->drupalGet('test-page');
$this->assertSession()->pageTextNotContains('Expected content', 'Page has expected content.');
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
