<?php

declare(strict_types=1);

namespace DrupalRector\Rector\ConstFetch;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Type\ObjectType;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://www.drupal.org/node/3151009
 *
 * @see \Rector\Tests\DrupalRector\Rector\ConstFetch\SymfonyCmfRouteObjectInterfaceConstantsRector\SymfonyCmfRouteObjectInterfaceConstantsRectorTest
 */
final class SymfonyCmfRouteObjectInterfaceConstantsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('"Replaces constant calls on \Symfony\Cmf\Component\Routing\RouteObjectInterface	to \Drupal\Core\Routing\RouteObjectInterface"', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public const NAME = \Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_NAME;
    public const OBJECT = \Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT;
    public const CONTROLLER = \Symfony\Cmf\Component\Routing\RouteObjectInterface::CONTROLLER_NAME;
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public const NAME = \Drupal\Core\Routing\RouteObjectInterface::ROUTE_NAME;
    public const OBJECT = \Drupal\Core\Routing\RouteObjectInterface::ROUTE_OBJECT;
    public const CONTROLLER = \Drupal\Core\Routing\RouteObjectInterface::CONTROLLER_NAME;
}
CODE_SAMPLE

            )
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [\PhpParser\Node\Expr\ClassConstFetch::class];
    }

    /**
     * @param \PhpParser\Node\Expr\ClassConstFetch $node
     */
    public function refactor(Node $node): ?Node
    {
        $cmfRouteObjectInterfaceType = new ObjectType(\Symfony\Cmf\Component\Routing\RouteObjectInterface::class);
        if (!$this->isObjectType($node->class, $cmfRouteObjectInterfaceType)) {
            return $node;
        }

        $node->class = new FullyQualified(\Drupal\Core\Routing\RouteObjectInterface::class);
        return $node;
    }
}
