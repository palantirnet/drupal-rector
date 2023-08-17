<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated module_load_install call with ModuleHandler call.
 */
class ModuleLoadInstallRector extends AbstractRector
{
    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node\Expr\CallLike
    {
        /** @var Node\Expr\FuncCall $node */
        if ($this->getName($node->name) === 'module_load_install') {
            $args = $node->getArgs();
            $args[] = new Node\Arg(new Node\Scalar\String_('install'));
            return new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal\Core\Extension\ModuleHandler'), 'loadInclude', $args);
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated module_load_install() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
module_load_install('example');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal\Core\Extension\ModuleHandler::loadInclude('example', 'install');
CODE_AFTER
            )
        ]);
    }

}
