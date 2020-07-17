<?php

namespace DrupalRector\Rector\Deprecation\Drupal9;

use DrupalRector\Utility\AddCommentTrait;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated \Drupal\Core\Database\Connection::$escapedNames references.
 *
 * This should probably be removed at some point. It's being added to test Drupal 9 integration because there are no great deprecations to pick from. :(
 *
 * See https://www.drupal.org/node/2986894 for change record.
 *
 * What is covered:
 * - We assume that we are checking the tables.
 *
 * Improvement opportunities
 * - Combine the tables and fields perhaps?
 */
final class ConnectionEscapedNamesRector extends AbstractRector {

  use AddCommentTrait;

  /**
   * @inheritdoc
   */
  public function getDefinition(): RectorDefinition {
    return new RectorDefinition('Fixes deprecated Connection::$escapedNames uses.',[
      new CodeSample(
        <<<'CODE_BEFORE'
$escaped_names = \Drupal\Core\Database\Connection::$escapedNames;
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
$escaped_names = \Drupal\Core\Database\Connection::$escapedTables;
// or
$escaped_names = \Drupal\Core\Database\Connection::$escapedFields;
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
      Node\Expr\StaticPropertyFetch::class,
    ];
  }

  /**
   * @inheritdoc
   */
  public function refactor(Node $node): ?Node
  {
    /** @var Node\Expr\StaticPropertyFetch $node */
    if ($this->getName($node->name) === 'escapedNames' && $this->getName($node->class) === 'Drupal\Core\Database\Connection') {

      $name = new Node\VarLikeIdentifier('escapedTables');

      $class = $node->class;

      $new_node = new Node\Expr\StaticPropertyFetch($class, $name);

      $this->addDrupalRectorComment($node, 'This is assuming we want to use `$escapedTables`, but you may need to use `$escapedFields` instead.');

      return $new_node;
    }

    return null;
  }

}
