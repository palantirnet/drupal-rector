<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated ModuleHandlerInterface::addModule() and addProfile() calls.
 *
 * These methods are no-ops since drupal:11.2.0 and removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3528899
 * @see https://www.drupal.org/node/3491200
 */
final class RemoveModuleHandlerAddModuleCallsRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Stmt\Expression::class];
    }

    /** @return NodeVisitor::REMOVE_NODE|null */
    public function refactor(Node $node): mixed
    {
        assert($node instanceof Node\Stmt\Expression);

        if (!$node->expr instanceof Node\Expr\MethodCall) {
            return null;
        }

        $methodCall = $node->expr;

        if (!$this->isNames($methodCall->name, ['addModule', 'addProfile'])) {
            return null;
        }

        $isModuleHandler = false;
        foreach (['Drupal\Core\Extension\ModuleHandlerInterface', 'Drupal\Core\Extension\ModuleHandler'] as $class) {
            if ($this->isObjectType($methodCall->var, new ObjectType($class))) {
                $isModuleHandler = true;
                break;
            }
        }
        if (!$isModuleHandler) {
            return null;
        }

        return NodeVisitor::REMOVE_NODE;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Removes deprecated ModuleHandlerInterface::addModule() and addProfile() calls, which are no-ops since drupal:11.2.0 and removed in drupal:12.0.0', [
            new CodeSample(
                "\$moduleHandler->addModule('mymodule', 'modules/mymodule');",
                ''
            ),
        ]);
    }
}
