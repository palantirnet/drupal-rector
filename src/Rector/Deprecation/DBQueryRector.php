<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\DBBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated db_query() calls.
 *
 * See https://www.drupal.org/node/2993033 for change record.
 *
 * What is covered:
 * - See `DBBase.php`
 *
 * Improvement opportunities
 *  - See `DBBase.php`
 */
final class DBQueryRector extends DBBase
{
  protected $deprecatedMethodName = 'db_query';

  protected $optionsArgumentPosition = 3;

  /**
   * @inheritdoc
   */
  public function getRuleDefinition(): RuleDefinition
  {
    return new RuleDefinition('Fixes deprecated db_query() calls',[
      new CodeSample(
        <<<'CODE_BEFORE'
db_query($query, $args, $options);
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
\Drupal::database()->query($query, $args, $options);
CODE_AFTER
      )
    ]);
  }

}
