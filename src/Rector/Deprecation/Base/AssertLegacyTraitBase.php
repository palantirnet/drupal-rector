<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

abstract class AssertLegacyTraitBase extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\MethodCall::class,
        ];
    }

    protected function createAssertSessionMethodCall(string $method, array $args): Node\Expr\MethodCall
    {
        $assertSessionNode = $this->nodeFactory->createLocalMethodCall('assertSession');
        return $this->nodeFactory->createMethodCall($assertSessionNode, $method, $args);
    }
}

