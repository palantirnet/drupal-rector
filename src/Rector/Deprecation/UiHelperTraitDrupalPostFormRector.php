<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\AddCommentTrait;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class UiHelperTraitDrupalPostFormRector extends AbstractRector
{

    use AddCommentTrait;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated UiHelperTrait::drupalPostForm() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$edit = [];
$edit['action'] = 'action_goto_action';
$this->drupalPostForm('admin/config/system/actions', $edit, 'Create');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$edit = [];
$edit['action'] = 'action_goto_action';
$this->drupalGet('admin/config/system/actions');
$this->submitForm($edit, 'Create');
CODE_AFTER
            )
        ]);
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Expr\MethodCall::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\MethodCall);
        if ($this->getName($node->name) === 'drupalPostForm') {
            [$path, $edit, $button] = $node->args;
            // @todo we _must_ inject drupalGet.
            // new Node\Expr\MethodCall($node->var, new Node\Identifier('drupalGet'), [$path])
            $this->addDrupalRectorComment(
                $node,
                sprintf('You must call `$this->drupalGet("%s");" before submitForm', $path->value)
            );
            return new Node\Expr\MethodCall($node->var, new Node\Identifier('submitForm'), [$edit, $button]);
        }
        return null;
    }
}
