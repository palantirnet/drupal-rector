<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\Deprecation;

use PhpParser\Node;
use Rector\Exception\ShouldNotHappenException;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class UiHelperTraitDrupalPostFormRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated UiHelperTrait::drupalPostForm() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$edit = [];
$edit['action'] = 'action_goto_action';
$this->drupalPostForm('admin/config/system/actions', $edit, 'Create');
$edit['action'] = 'action_goto_action_1';
$this->drupalPostForm(null, $edit, 'Edit');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$edit = [];
$edit['action'] = 'action_goto_action';
$this->drupalGet('admin/config/system/actions');
$this->submitForm($edit, 'Create');
$edit['action'] = 'action_goto_action_1';
$this->submitForm($edit, 'Edit');
CODE_AFTER
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\Expression::class,
        ];
    }

    /**
     * @param Node\Expr\MethodCall $node
     *
     * @throws ShouldNotHappenException
     *
     * @return array<int, ?\PhpParser\Node\Arg>
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

    public function refactor(Node $node)
    {
        assert($node instanceof Node\Stmt\Expression);

        if (!($node->expr instanceof Node\Expr\MethodCall)) {
            return null;
        }

        if ($this->getName($node->expr->name) === 'drupalPostForm') {
            /** @var Node\Stmt[] $nodes */
            $nodes = [];
            [$path, $edit, $button, $options, $htmlId] = $this->safeArgDestructure($node->expr);

            if ($htmlId === null) {
                $submitFormNode = $this->nodeFactory->createLocalMethodCall('submitForm', [$edit, $button]);
            } else {
                $submitFormNode = $this->nodeFactory->createLocalMethodCall('submitForm', [$edit, $button, $htmlId]);
            }

            $pathValue = $path->value;
            if (!$pathValue instanceof Node\Expr\ConstFetch || strtolower((string) $pathValue->name) !== 'null') {
                if ($options === null) {
                    $drupalGetNode = $this->nodeFactory->createLocalMethodCall('drupalGet', [$path]);
                } else {
                    $drupalGetNode = $this->nodeFactory->createLocalMethodCall('drupalGet', [$path, $options]);
                }
                $nodes[] = new Node\Stmt\Expression($drupalGetNode);
            }
            $nodes[] = new Node\Stmt\Expression($submitFormNode);

            return $nodes;
        }

        return null;
    }
}
