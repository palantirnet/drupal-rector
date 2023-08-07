<?php

declare(strict_types=1);

namespace DrupalRector\Rector;

use Drupal\Core\Component\DeprecationHelper;
use DrupalRector\Contract\DrupalCoreRectorInterface;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Rector\Php72\NodeFactory\AnonymousFunctionFactory;

abstract class AbstractDrupalCoreRector extends AbstractRector implements DrupalCoreRectorInterface
{
    private AnonymousFunctionFactory $anonymousFunctionFactory;

    public function __construct(AnonymousFunctionFactory $anonymousFunctionFactory) {
        $this->anonymousFunctionFactory = $anonymousFunctionFactory;
    }

    public function refactor(Node $node)
    {
        if (version_compare(\Drupal::VERSION, $this->getVersion(), '<')) {
            return null;
        }

        $result = $this->doRefactor($node);

        if ($result === null) {
            return $result;
        }

        if($node instanceof Node\Expr\CallLike && $result instanceof Node\Expr\CallLike) {
            return $this->createBcCallOnCallLike($node, $result);
        }

        return $result;
    }

    /**
     * Process Node of matched type
     * @return Node|Node[]|null
     */
    abstract protected function doRefactor(Node $node);

    private function createBcCallOnCallLike(Node\Expr\CallLike $node, Node\Expr\CallLike $result): Node\Expr\StaticCall
    {
        $clonedNode = clone $node;
        return $this->nodeFactory->createStaticCall(DeprecationHelper::class, 'backwardsCompatibleCall', [
            $this->nodeFactory->createClassConstFetch(\Drupal::class, 'VERSION'),
            $this->getVersion(),
            $this->anonymousFunctionFactory->create([], [new Node\Stmt\Return_($clonedNode)], null),
            $this->anonymousFunctionFactory->create([], [new Node\Stmt\Return_($result)], null),
        ]);
    }

}
