<?php

declare(strict_types=1);

namespace DrupalRector\Rector\FuncCall;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://www.drupal.org/node/3220952
 *
 * @see \DrupalRector\Tests\Rector\FuncCall\ModuleLoadInstallRector\ModuleLoadInstallRectorTest
 */
final class ModuleLoadInstallRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('"module_load_install() replaced with ModuleHandler::loadInclude"', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        module_load_install('node');
    }
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        \Drupal::moduleHandler()->loadInclude('node', 'install');
    }
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
        return [\PhpParser\Node\Expr\FuncCall::class];
    }

    /**
     * @param \PhpParser\Node\Expr\FuncCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->nodeNameResolver->getName($node) !== 'module_load_install') {
            return null;
        }
        $args = $node->getArgs();
        $args[] = new Node\Arg(new Node\Scalar\String_('install'));

        $moduleHandlerCall = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            'moduleHandler'
        );

        return new Node\Expr\MethodCall(
            $moduleHandlerCall,
            new Node\Identifier('loadInclude'),
            $args
        );
    }
}
