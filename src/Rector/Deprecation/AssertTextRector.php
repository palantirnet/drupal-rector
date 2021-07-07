<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use DrupalRector\Utility\AddCommentTrait;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertTextRector extends AssertLegacyTraitBase
{

    use AddCommentTrait;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated UiHelperTrait::drupalPostForm() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->drupalGet('test-page');
$this->assertText('Expected content', 'Page has expected content.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->drupalGet('test-page');
$this->assertSession()->pageTextContains('Expected content', 'Page has expected content.');
CODE_AFTER
            )
        ]);
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\MethodCall);
        if ($this->getName($node->name) !== 'assertText') {
            return null;
        }
        $this->addDrupalRectorComment(
            $node,
            'Verify the assertion: pageTextContains() for HTML responses, responseContains() for non-HTML responses.'
            . PHP_EOL . '// The passed text should be HTML decoded, exactly as a human sees it in the browser.'
        );
        // We do not pass the full `$node->args` to the new method call, as the
        // legacy assert from Simpletest used to support a message. In fact,
        // assertText dropped that, but many code bases still have a second
        // argument for the message. Let's help them drop it.
        return $this->createAssertSessionMethodCall('pageTextContains', [$node->args[0]]);
    }
}
