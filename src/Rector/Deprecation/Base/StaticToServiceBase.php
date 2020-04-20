<?php

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

/**
 * Replaces deprecated static call with service method call.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
abstract class StaticToServiceBase extends AbstractRector
{
    /**
     * Deprecated fully qualified class name.
     *
     * @var string
     */
    protected $deprecatedFullyQualifiedClassName;

    /**
     * The deprecated function name.
     *
     * @var string
     */
    protected $deprecatedMethodName;

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
            Node\Expr\StaticCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\StaticCall $node */
        if ($this->getName($node->name) === $this->deprecatedMethodName && $this->getName($node->class) === $this->deprecatedFullyQualifiedClassName) {

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
