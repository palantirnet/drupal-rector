<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use DrupalRector\Services\AddCommentService;
use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated function call to EntityInterface::link.
 *
 * See https://www.drupal.org/node/2614344 for change record.
 *
 * What is covered:
 * - Changes the name of the method and adds toString().
 *
 * Improvement opportunities:
 * - Checks the variable has a certain class.
 */
final class EntityInterfaceLinkRector extends AbstractRector
{
    /**
     * @var AddCommentService
     */
    private AddCommentService $commentService;

    public function __construct(AddCommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated link() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$url = $entity->link();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$url = $entity->toLink()->toString();
CODE_AFTER
            ),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\Expression::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Stmt\Expression);

        if (!($node->expr instanceof Node\Expr\MethodCall) && !($node->expr instanceof Node\Expr\Assign && $node->expr->expr instanceof Node\Expr\MethodCall)) {
            return null;
        }

        if ($node->expr instanceof Node\Expr\MethodCall && $this->getName($node->expr->name) !== 'link') {
            return null;
        }

        if (($node->expr instanceof Node\Expr\Assign && $node->expr->expr instanceof Node\Expr\MethodCall) && $this->getName($node->expr->expr->name) !== 'link') {
            return null;
        }

        if ($node->expr instanceof Node\Expr\MethodCall) {
            $methodCall = $this->getMethodCall($node->expr, $node);
            $node->expr = $methodCall;

            return $node;
        }

        if ($node->expr instanceof Node\Expr\Assign && $node->expr->expr instanceof Node\Expr\MethodCall) {
            $methodCall = $this->getMethodCall($node->expr->expr, $node);
            $node->expr->expr = $methodCall;

            return $node;
        }

        return null;
    }

    public function getMethodCall(Node\Expr\MethodCall $expr, Node\Stmt\Expression $node): Node\Expr\MethodCall
    {
        $node_class_name = $this->getName($expr->var);

        $this->commentService->addDrupalRectorComment($node,
            "Please confirm that `$$node_class_name` is an instance of `\Drupal\Core\Entity\EntityInterface`. Only the method name and not the class name was checked for this replacement, so this may be a false positive.");

        $toLink_node = $expr;

        $toLink_node->name = new Node\Identifier('toLink');

        // Add ->toString();
        $new_node = new Node\Expr\MethodCall($toLink_node,
            new Node\Identifier('toString'));

        return $new_node;
    }
}
