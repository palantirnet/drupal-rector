<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\ConstantToClassConstantBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated FILE_EXISTS_REPLACE constant use.
 *
 * See https://www.drupal.org/node/3006851 for change record.
 *
 * What is covered:
 * - See `ConstantToClassConstantBase.php`
 *
 * Improvement opportunities
 *  - See `ConstantToClassConstantBase.php`
 */
final class FileExistsReplaceRector extends ConstantToClassConstantBase
{
  protected $deprecatedConstant = 'FILE_EXISTS_REPLACE';

  protected $constantFullyQualifiedClassName = 'Drupal\Core\File\FileSystemInterface';

  protected $constant = 'EXISTS_REPLACE';


  /**
   * @inheritdoc
   */
  public function getRuleDefinition(): RuleDefinition
  {
    return new RuleDefinition('Fixes deprecated FILE_EXISTS_REPLACE use',[
      new CodeSample(
        <<<'CODE_BEFORE'
$result = file_copy($file, $dest, FILE_EXISTS_REPLACE);
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
$result = file_copy($file, $dest, \Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE);
CODE_AFTER
      )
    ]);
  }
}
