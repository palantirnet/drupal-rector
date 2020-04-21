<?php

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

/**
 * Renames deprecated the argument in a static call.
 */
abstract class StaticArgumentRenameBase extends AbstractRector {

  /**
   * The fully qualified class name.
   *
   * @var string
   */
  protected $fullyQualifiedClassName;

  /**
   * The method name.
   *
   * @var string
   */
  protected $methodName;

  /**
   * The deprecated argument.
   *
   * @var string.
   */
  protected $deprecatedArgument;

  /**
   * The replacement argument.
   *
   * @var string.
   */
  protected $argument;

  /**
   * @inheritdoc
   */
  public function getNodeTypes(): array {
    return [
      Node\Expr\StaticCall::class
    ];
  }

  /**
   * @inheritdoc
   */
  public function refactor(Node $node): ?Node {

    if ($node instanceof Node\Expr\StaticCall) {
      /** @var Node\Expr\StaticCall $node */
      if ($this->getName($node->name) === $this->methodName && (string) $node->class === $this->fullyQualifiedClassName) {

        if (count($node->args) === 1) {
          /* @var Node\Arg $argument */
          $argument = $node->args[0];

          if ($argument->value instanceof Node\Scalar\String_ && $argument->value->value === $this->deprecatedArgument) {
            $node->args[0] = new Node\Arg(new Node\Scalar\String_($this->argument));

            return $node;
          }
        }
      }
    }

    return null;
  }
}
