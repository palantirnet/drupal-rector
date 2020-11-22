<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\DBBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated db_drop_table() calls.
 *
 * See https://www.drupal.org/node/2987737 for change record.
 *
 * What is covered:
 * - See `DBBase.php`
 *
 * Improvement opportunities
 *  - See `DBBase.php`
 */
final class DBDropTableRector extends DBBase
{
  protected $deprecatedMethodName = 'db_drop_table';

  protected $optionsArgumentPosition = 1;

  /**
   * @inheritdoc
   */
  public function getDefinition(): RectorDefinition
  {
    return new RectorDefinition('Fixes deprecated db_drop_table() calls',[
      new CodeSample(
        <<<'CODE_BEFORE'
db_drop_table($table);
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
\Drupal::database()->schema()->dropTable($table);
CODE_AFTER
      )
    ]);
  }

}
