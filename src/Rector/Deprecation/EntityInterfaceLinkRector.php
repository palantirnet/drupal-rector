<?php

namespace DrupalRector\Rector\Deprecation;

use Rector\Core\RectorDefinition\RectorDefinition;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

/**
 * Replaces deprecated function call to EntityInterface::link.
 *
 * See https://www.drupal.org/node/2614344 for change record.
 *
 * What is covered:
 * - Changes the name of the method and adds toString().
 *
 * Improvement opportunities:
 * - Checks the variable has a certain class.
 */
final class EntityInterfaceLinkRector extends AbstractRector
{
    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated link() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
$url = $entity->link();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$url = $entity->toLink()->toString();
CODE_AFTER
        )
      ]);
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
      /** @var Node\Expr\MethodCall $node */
      // TODO: Check the class to see if it implements Drupal\Core\Entity\EntityInterface.
      if ($this->getName($node->name) === 'link') {
          $toLink_node = $node;

          $toLink_node->name = new Node\Name('toLink');

          // Add ->toString();
          $new_node = new Node\Expr\MethodCall($toLink_node, new Node\Identifier('toString'));

          return $new_node;
      }

      return null;
  }

}
