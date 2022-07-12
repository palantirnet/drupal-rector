<?php

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeCollector\ScopeResolver\ParentClassScopeResolver;
use Rector\NodeTypeResolver\Node\AttributeKey;

/**
 * Replaces deprecated getMock() calls in classes.
 *
 * See https://www.drupal.org/node/2907725 for change record.
 *
 * What is covered:
 * - Checks the class being extended.
 */
abstract class GetMockBase extends AbstractRector
{

  /**
   * The fully qualified base class our classes are extending.
   *
   * @var string
   */
  protected $baseClassBeingExtended;

  /**
   * @var ParentClassScopeResolver
   */
  protected $parentClassScopeResolver;

  public function __construct(ParentClassScopeResolver $parentClassScopeResolver) {
      $this->parentClassScopeResolver = $parentClassScopeResolver;
  }

    /**
   * @inheritdoc
   */
  public function getNodeTypes(): array
  {
    return [
      Node\Expr\MethodCall::class,
    ];
  }

  /**
   * @inheritdoc
   */
  public function refactor(Node $node): ?Node
  {
    $scope = $node->getAttribute(AttributeKey::SCOPE);
    if (!$scope instanceof Scope) {
      return null;
    }

    $parentClassName = $this->parentClassScopeResolver->resolveParentClassName($scope);
    /* @var Node\Expr\MethodCall $node */
    // This checks for a method call with the method name of `getMock` and that
    // the variable calling `getMock` is `$this`, not some other variable call,
    // such as `$myOtherService->getMock` and have unintended consequences.
    if ($this->getName($node->name) === 'getMock'
        && ($node->var instanceof Node\Expr\Variable)
        && $this->getName($node->var) === 'this'
        && $parentClassName === $this->baseClassBeingExtended
    ) {

      // Build the arguments.
      $method_arguments = $node->args;

      // Get the updated method name.
      $method_name = new Node\Identifier('createMock');

      $node = new Node\Expr\MethodCall($node->var, $method_name, $method_arguments);

      return $node;
    }

    return null;
  }
}
