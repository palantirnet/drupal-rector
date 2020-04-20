<?php

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

/**
 * Replaces deprecated function call with service method call.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
abstract class FunctionToServiceBase extends AbstractRector
{
    /**
     * The deprecated function name.
     *
     * @var string
     */
    protected $deprecatedFunctionName;

    /**
     * The replacement service name.
     *
     * @var string
     */
    protected $serviceName;

    /**
     * The replacement service method.
     *
     * @var string
     */
    protected $serviceMethodName;

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
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\FuncCall $node */
        if ($this->getName($node->name) === $this->deprecatedFunctionName) {

            // This creates a service call like `\Drupal::service('file_system').
            // TODO use dependency injection.
            $service = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'service', [new Node\Arg(new Node\Scalar\String_($this->serviceName))]);

            $method_name = new Node\Identifier($this->serviceMethodName);

            $node = new Node\Expr\MethodCall($service, $method_name, $node->args);

            return $node;
        }

        return null;
    }
}
