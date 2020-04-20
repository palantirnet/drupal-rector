<?php

namespace DrupalRector\Rector\Deprecation\Base;

use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

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
  public function getDefinition(): RectorDefinition {
    return new RectorDefinition('Renames the IDs in Drupal::service() calls',[
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
