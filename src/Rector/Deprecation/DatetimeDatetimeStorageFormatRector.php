<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\ConstantToClassConstantBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated DATETIME_DATETIME_STORAGE_FORMAT constant use.
 *
 * See https://www.drupal.org/node/2912980 for change record.
 *
 * What is covered:
 * - See `ConstantToClassConstantBase.php`
 *
 * Improvement opportunities
 *  - See `ConstantToClassConstantBase.php`
 */
final class DatetimeDatetimeStorageFormatRector extends ConstantToClassConstantBase
{
  protected $deprecatedConstant = 'DATETIME_DATETIME_STORAGE_FORMAT';

  protected $constantFullyQualifiedClassName = 'Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface';

  protected $constant = 'DATETIME_STORAGE_FORMAT';


  /**
   * @inheritdoc
   */
  public function getDefinition(): RectorDefinition
  {
    return new RectorDefinition('Fixes deprecated DATETIME_DATETIME_STORAGE_FORMAT use',[
      new CodeSample(
        <<<'CODE_BEFORE'
use Drupal\Core\Datetime\DrupalDateTime;
$date = new DrupalDateTime('now', new \DateTimezone('America/Los_Angeles'));
$now = $date->format(DATETIME_DATETIME_STORAGE_FORMAT);
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
$date = new DrupalDateTime('now', new \DateTimezone('America/Los_Angeles'));
$now = $date->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
CODE_AFTER
      )
    ]);
  }
}
