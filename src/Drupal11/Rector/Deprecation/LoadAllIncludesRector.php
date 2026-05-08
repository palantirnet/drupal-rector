<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated ModuleHandler::loadAllIncludes() with an explicit foreach loop.
 *
 * @see https://www.drupal.org/node/3536431
 * @see https://www.drupal.org/node/3536432
 */
final class LoadAllIncludesRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Stmt\Expression::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Node\Stmt\Expression) {
            return null;
        }

        if (!$node->expr instanceof Node\Expr\MethodCall) {
            return null;
        }

        $methodCall = $node->expr;

        if (!$this->isName($methodCall->name, 'loadAllIncludes')) {
            return null;
        }

        if (!$this->isObjectType($methodCall->var, new ObjectType('Drupal\Core\Extension\ModuleHandlerInterface'))) {
            return null;
        }

        $caller = $methodCall->var;
        $getModuleListCall = new Node\Expr\MethodCall(clone $caller, 'getModuleList');

        $moduleVar = new Node\Expr\Variable('module');
        $filenameVar = new Node\Expr\Variable('filename');

        $loadIncludeArgs = [new Node\Arg($moduleVar)];
        foreach ($methodCall->args as $arg) {
            $loadIncludeArgs[] = $arg;
        }

        $loadIncludeCall = new Node\Expr\MethodCall(clone $caller, 'loadInclude', $loadIncludeArgs);

        return new Node\Stmt\Foreach_(
            $getModuleListCall,
            $filenameVar,
            [
                'keyVar' => $moduleVar,
                'stmts' => [new Node\Stmt\Expression($loadIncludeCall)],
            ]
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replaces deprecated ModuleHandler::loadAllIncludes() with getModuleList() + loadInclude() loop', [
            new CodeSample(
                "\$this->moduleHandler->loadAllIncludes('install');",
                "foreach (\$this->moduleHandler->getModuleList() as \$module => \$filename) {\n    \$this->moduleHandler->loadInclude(\$module, 'install');\n}"
            ),
        ]);
    }
}
