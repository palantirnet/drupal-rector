<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\DBBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated db_update() calls.
 *
 * See https://www.drupal.org/node/2993033 for change record.
 *
 * What is covered:
 * - See `DBBase.php`
 *
 * Improvement opportunities
 *  - See `DBBase.php`
 */
final class DBUpdateRector extends DBBase
{
  protected $deprecatedMethodName = 'db_update';

  protected $optionsArgumentPosition = 2;

  /**
   * @inheritdoc
   */
  public function getRuleDefinition(): RuleDefinition
  {
    return new RuleDefinition('Fixes deprecated db_update() calls',[
      new CodeSample(
        <<<'CODE_BEFORE'
db_update($table, $options);
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
\Drupal::database()->update($table, $options);
CODE_AFTER
      )
    ]);
  }

}
