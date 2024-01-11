<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated module_load_install call with ModuleHandler call.
 */
class ModuleLoadRector extends AbstractRector
{
    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function refactor(Node $node): ?Node\Expr\CallLike
    {
        /** @var Node\Expr\FuncCall $node */
        if ($this->getName($node->name) === 'module_load_install') {
            $args = $node->getArgs();
            $args[] = new Node\Arg(new Node\Scalar\String_('install'));

            return $this->nodeFactory->createMethodCall($this->nodeFactory->createStaticCall('Drupal', 'moduleHandler'), 'loadInclude', $args);
        } elseif ($this->getName($node->name) === 'module_load_include') {
            $newArgs = $args = $node->getArgs();
            $newArgs[0] = $args[1];
            $newArgs[1] = $args[0];

            return $this->nodeFactory->createMethodCall($this->nodeFactory->createStaticCall('Drupal', 'moduleHandler'), 'loadInclude', $newArgs);
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated module_load_install() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
module_load_install('example');
$type = 'install';
$module = 'example';
$name = 'name';
module_load_include($type, $module, $name);
module_load_include($type, $module);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::moduleHandler()->loadInclude('example', 'install');
$type = 'install';
$module = 'example';
$name = 'name';
\Drupal::moduleHandler()->loadInclude($module, $type, $name);
\Drupal::moduleHandler()->loadInclude($module, $type);
CODE_AFTER
            ),
        ]);
    }
}
