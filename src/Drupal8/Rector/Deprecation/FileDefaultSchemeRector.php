<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the deprecated file_default_scheme function calls.
 *
 * @see https://www.drupal.org/node/3049030 for change record.
 */
final class FileDefaultSchemeRector extends AbstractRector
{
    protected string $deprecatedFunctionName = 'file_default_scheme';

    protected string $configObject = 'system.file';

    protected string $configName = 'default_scheme';

    /**
     * {@inheritDoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\FuncCall $node */
        if ($this->getName($node->name) !== $this->deprecatedFunctionName) {
            return null;
        }
        $static_function_args = [new Node\Arg(new Node\Scalar\String_($this->configObject))];

        $static_function = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'config', $static_function_args);

        $method_name = new Node\Identifier('get');
        $method_args = [new Node\Arg(new Node\Scalar\String_($this->configName))];

        $node = new Node\Expr\MethodCall($static_function, $method_name, $method_args);

        return $node;
    }

    /**
     * {@inheritDoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_default_scheme calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$file_default_scheme = file_default_scheme();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$file_default_scheme = \Drupal::config('system.file')->get('default_scheme');
CODE_AFTER
            ),
        ]);
    }
}
