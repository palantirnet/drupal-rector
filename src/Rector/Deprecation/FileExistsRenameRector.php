<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\ConstantToClassConstantBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated FILE_EXISTS_RENAME constant use.
 *
 * See https://www.drupal.org/node/3006851 for change record.
 *
 * What is covered:
 * - See `ConstantToClassConstantBase.php`
 *
 * Improvement opportunities
 *  - See `ConstantToClassConstantBase.php`
 */
final class FileExistsRenameRector extends ConstantToClassConstantBase
{
  protected $deprecatedConstant = 'FILE_EXISTS_RENAME';

  protected $constantFullyQualifiedClassName = 'Drupal\Core\File\FileSystemInterface';

  protected $constant = 'EXISTS_RENAME';


  /**
   * @inheritdoc
   */
  public function getRuleDefinition(): RuleDefinition
  {
    return new RuleDefinition('Fixes deprecated FILE_EXISTS_RENAME use',[
      new CodeSample(
        <<<'CODE_BEFORE'
$result = file_unmanaged_copy($source, $destination, FILE_EXISTS_RENAME);
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
$result = file_unmanaged_copy($source, $destination, \Drupal\Core\File\FileSystemInterface::EXISTS_RENAME);
CODE_AFTER
      )
    ]);
  }
}
