<?php

declare(strict_types=1);

namespace DrupalRector\Rector;

use DrupalRector\Contract\DrupalCoreRectorInterface;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Rector\PostRector\Collector\NodesToAddCollector;

abstract class AbstractDrupalCoreRector extends AbstractRector implements DrupalCoreRectorInterface
{


    /**
     * @readonly
     * @var \Rector\PostRector\Collector\NodesToAddCollector
     */
    private $nodesToAddCollector;

    public function __construct(NodesToAddCollector $nodesToAddCollector)
    {
        $this->nodesToAddCollector = $nodesToAddCollector;
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
        $versionCompare = $this->nodeFactory->createFuncCall(
            'version_compare',
            [
                $this->nodeFactory->createClassConstFetch(\Drupal::class, 'VERSION'),
                $this->getVersion(),
                '>=',
            ]
        );
        $if = new Node\Stmt\If_($versionCompare, [
            'stmts' => [new Node\Stmt\Expression($result)],
            'else' => new Node\Stmt\Else_([
                new Node\Stmt\Expression($node)
            ]),
        ]);
        $this->nodesToAddCollector->addNodeBeforeNode($if, $node);
        return $result;
    }

    /**
     * Process Node of matched type
     * @return Node|Node[]|null
     */
    abstract protected function doRefactor(Node $node);

}
