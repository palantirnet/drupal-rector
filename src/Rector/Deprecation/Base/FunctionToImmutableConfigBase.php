<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;


/**
 * Replace deprecated function calls with a wrapped config.factory service call to get an immutable configuration.
 *
 * @see \DrupalRector\Rector\Deprecation\FileDefaultScheme for an example.
 */
abstract class FunctionToImmutableConfigBase extends AbstractRector
{
    /**
     * @inheritDoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    /**
     * @inheritDoc
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
}
