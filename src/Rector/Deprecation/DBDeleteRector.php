<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\DBBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated db_delete() calls.
 *
 * See https://www.drupal.org/node/2993033 for change record.
 *
 * What is covered:
 * - See `DBBase.php`
 *
 * Improvement opportunities
 *  - See `DBBase.php`
 */
final class DBDeleteRector extends DBBase
{
  protected $deprecatedMethodName = 'db_delete';

  protected $optionsArgumentPosition = 2;

  /**
   * @inheritdoc
   */
  public function getRuleDefinition(): RuleDefinition
  {
    return new RuleDefinition('Fixes deprecated db_delete() calls',[
      new CodeSample(
        <<<'CODE_BEFORE'
db_delete($table, $options);
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
\Drupal::database()->delete($table, $options);
CODE_AFTER
      )
    ]);
  }

}
