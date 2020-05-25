<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;


/**
 * Replace deprecated function calls with a static config.factory service call to get an immutable configuration.
 *
 * @see \DrupalRector\Rector\Deprecation\FileDefaultSchemeRector for an example.
 *
 * What is covered:
 *  - Static replacement
 */
abstract class FunctionToImmutableConfigBase extends AbstractRector
{
    /**
     * Deprecated function name.
     *
     * Example: file_default_scheme
     *
     * @var string
     */
    protected $deprecatedFunctionName = '';

    /**
     * The name of the configuration object. The name corresponds to a configuration file.
     *
     * Example: system.file.
     *
     * @var string
     */
    protected $configObject = '';

    /**
     * A string that maps to a key within the configuration data.
     *
     * Example: default_scheme
     *
     * @var string
     */
    protected $configName = '';

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
