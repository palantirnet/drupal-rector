<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\ConstantToClassConstantBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated DATETIME_STORAGE_TIMEZONE constant use.
 *
 * See https://www.drupal.org/node/2912980 for change record.
 *
 * What is covered:
 * - See `ConstantToClassConstantBase.php`
 *
 * Improvement opportunities
 *  - See `ConstantToClassConstantBase.php`
 */
final class DatetimeStorageTimezoneRector extends ConstantToClassConstantBase
{
  protected $deprecatedConstant = 'DATETIME_STORAGE_TIMEZONE';

  protected $constantFullyQualifiedClassName = 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface';

  protected $constant = 'STORAGE_TIMEZONE';


  /**
   * @inheritdoc
   */
  public function getRuleDefinition(): RuleDefinition
  {
    return new RuleDefinition('Fixes deprecated DATETIME_STORAGE_TIMEZONE use',[
      new CodeSample(
        <<<'CODE_BEFORE'
$timezone = new \DateTimeZone(DATETIME_STORAGE_TIMEZONE);
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
$timezone = new \DateTimeZone(DateTimeItemInterface::STORAGE_TIMEZONE);
CODE_AFTER
      )
    ]);
  }
}
