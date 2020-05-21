<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated SafeMarkup::format() calls.
 *
 * See https://www.drupal.org/node/2549395 for change record.
 *
 * What is covered:
 * - Replace with a new object.
 *
 * Improvement opportunities
 */
final class SafeMarkupFormatRector extends AbstractRector
{

  /**
   * @inheritdoc
   */
  public function getDefinition(): RectorDefinition
  {
    return new RectorDefinition('Fixes deprecated SafeMarkup::format() calls',[
      new CodeSample(
        <<<'CODE_BEFORE'
$safe_string_markup_object = \Drupal\Component\Utility\SafeMarkup::format('hello world');
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
$safe_string_markup_object = new \Drupal\Component\Render\FormattableMarkup('hello world');
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
      Node\Expr\StaticCall::class,
    ];
  }

  /**
   * @inheritdoc
   */
  public function refactor(Node $node): ?Node
  {
    /** @var Node\Expr\StaticCall $node */
    if ($this->getName($node->name) === 'format' && $this->getName($node->class) === 'Drupal\Component\Utility\SafeMarkup') {

      $class = new Node\Name\FullyQualified('Drupal\Component\Render\FormattableMarkup');

      $new_node = new Node\Expr\New_($class, $node->args);

      return $new_node;
    }

    return null;
  }

}
