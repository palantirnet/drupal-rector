<?php

declare(strict_types=1);

namespace Mxr576\Rector\CodeStyle;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces define() with const.
 *
 * @see https://www.drupal.org/docs/develop/standards/coding-standards#naming
 */
final class DefineToConstRector extends AbstractRector
{
    /**
     * @inheritDoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Replaces define() with const.', [
            new CodeSample('define("FOOO", "BAR");', 'const FOOO = "BAR";'),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @inheritDoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var \PhpParser\Node\Expr\FuncCall $node */
        if (!$this->isName($node, 'define')) {
            return null;
        }

        $constNode = new Node\Stmt\Const_([
            new Node\Const_($node->args[0]->value->value, $node->args[1]->value),
        ]);

        $this->addNodeAfterNode($constNode, $node);

        $this->removeNode($node);

        return $node;
    }
}
