<?php

namespace DrupalRector\Rector\Deprecation\Base;

use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Renames deprecated Drupal::services().
 *
 * What is covered:
 *  - Renames argument in Drupal::service() calls.
 */
abstract class DrupalServiceRenameBase extends StaticArgumentRenameBase {

  /**
   * The fully qualified class name.
   *
   * @var string
   */
  protected $fullyQualifiedClassName = 'Drupal';

  /**
   * The method name.
   *
   * @var string
   */
  protected $methodName = 'service';

  /**
   * @inheritdoc
   */
  public function getRuleDefinition(): RuleDefinition {
    return new RuleDefinition('Renames the IDs in Drupal::service() calls',[
      new CodeSample(
        <<<'CODE_BEFORE'
\Drupal::service('old')->foo();
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
\Drupal::service('bar')->foo();
CODE_AFTER
      )
    ]);
  }

}
