<?php

declare(strict_types=1);

namespace DrupalRector\Rector\BestPractice;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use PhpParser\Node\Expr\FuncCall;
use Rector\NodeTypeResolver\Node\AttributeKey;
use PhpParser\Node\Stmt\Class_;

/**
 * Replaces t() with $this->t().
 */
final class UseThisTInsteadOfTRector extends AbstractRector
{

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Turns static t calls into $this->t.',
            [
                new ConfiguredCodeSample(
                    't("Text");',
                    '$this->t("Text");',
                    ['t' => '$this->t']
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param FuncCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node, 't')) {
            return null;
        }

        // not to refactor here
        $isVirtual = (bool)$node->name->getAttribute(
            AttributeKey::VIRTUAL_NODE
        );
        if ($isVirtual) {
            return null;
        }

        $parentFunction = $this->betterNodeFinder->findParentType($node, Node\Stmt\ClassMethod::class);
        if (!$parentFunction instanceof Node\Stmt\ClassMethod || $parentFunction->isStatic()) {
            return null;
        }

        $class = $this->betterNodeFinder->findParentType($node, Class_::class);
        if (!$class instanceof Class_) {
            return null;
        }

        $className = (string) $this->nodeNameResolver->getName($class);
        if (method_exists($className, 't')) {
            return new Node\Expr\MethodCall(
                new Node\Expr\Variable('this'),
                't',
                $node->args
            );
        }
        return null;
    }
}
