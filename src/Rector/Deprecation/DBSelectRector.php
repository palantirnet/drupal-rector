<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\DBBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated db_select() calls.
 *
 * See https://www.drupal.org/node/2993033 for change record.
 *
 * What is covered:
 * - See `DBBase.php`
 *
 * Improvement opportunities
 *  - See `DBBase.php`
 */
final class DBSelectRector extends DBBase
{
  protected $deprecatedMethodName = 'db_select';

  protected $optionsArgumentPosition = 3;

  /**
   * @inheritdoc
   */
  public function getDefinition(): RectorDefinition
  {
    return new RectorDefinition('Fixes deprecated db_select() calls',[
      new CodeSample(
        <<<'CODE_BEFORE'
db_select($table, $alias, $options);
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
\Drupal::database()->select($table, $alias, $options);
CODE_AFTER
      )
    ]);
  }

}
