<?php

declare(strict_types=1);

namespace DrupalRector\Rector;

use DrupalRector\Contract\DrupalCoreRectorInterface;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

abstract class AbstractDrupalCoreRector extends AbstractRector implements DrupalCoreRectorInterface
{

    public function refactor(Node $node)
    {
        if (version_compare(\Drupal::VERSION, $this->getVersion(), '<')) {
            return null;
        }
        $result = $this->doRefactor($node);
        if ($result === null) {
            return $result;
        }
        // TODO: add BC compatibility layer here
        return $result;
    }

    /**
     * Process Node of matched type
     * @return Node|Node[]|null
     */
    abstract protected function doRefactor(Node $node);

}
