<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\AddCommentTrait;
use PhpParser\Node;
use Rector\Core\Exception\ShouldNotHappenException;
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

    /**
     * @param \PhpParser\Node\Expr\MethodCall $node
     *
     * @return \PhpParser\Node\Arg[]
     * @throws \Rector\Core\Exception\ShouldNotHappenException
     */
    private function safeArgDestructure(Node\Expr\MethodCall $node): array
    {
        $count = count($node->args);
        if ($count === 3) {
            [$path, $edit, $button] = $node->args;
            return [$path, $edit, $button, null, null];
        }

        if ($count === 4) {
            [$path, $edit, $button, $options] = $node->args;
            return [$path, $edit, $button, $options, null];
        }

        if ($count === 5) {
            return $node->args;
        }

        throw new ShouldNotHappenException('Unexpected argument count for drupalPostForm');
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\MethodCall);
        if ($this->getName($node->name) === 'drupalPostForm') {
            [$path, $edit, $button, $options, $htmlId] = $this->safeArgDestructure($node);
            // @todo we _must_ inject drupalGet.
            // new Node\Expr\MethodCall($node->var, new Node\Identifier('drupalGet'), [$path])
            $pathValue = $path->value;
            assert($pathValue instanceof Node\Scalar\String_);

            if ($options === null) {
                $this->addDrupalRectorComment(
                    $node,
                    sprintf('You must call `$this->drupalGet("%s");" before submitForm', $pathValue->value)
                );
            } else {
                if ($options->value instanceof Node\Expr\Array_) {
                    $optionsArgs = array_map(function (Node\Expr\ArrayItem $item) {
                        $key = $this->valueResolver->getValue($item->key);
                        $value = $this->valueResolver->getValue($item->value);
                        return "'$key' => '$value'";
                    }, $options->value->items);
                    $optionsValue = '[' . implode(', ', $optionsArgs) . ']';
                } elseif ($options->value instanceof Node\Expr\Variable) {
                    $optionsValue = '$' . $options->value->name;
                } else {
                    throw new ShouldNotHappenException(
                        'Unexpected argument type passed to $options for drupalPostForm'
                    );
                }
                $this->addDrupalRectorComment(
                    $node,
                    sprintf(
                        'You must call `$this->drupalGet("%s", %s);" before submitForm',
                        $pathValue->value,
                        $optionsValue
                    )
                );
            }

            if ($htmlId === null) {
                return new Node\Expr\MethodCall($node->var, new Node\Identifier('submitForm'), [$edit, $button]);
            }
            return new Node\Expr\MethodCall($node->var, new Node\Identifier('submitForm'), [$edit, $button, $htmlId]);
        }
        return null;
    }
}
