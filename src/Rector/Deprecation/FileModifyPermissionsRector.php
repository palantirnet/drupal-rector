<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\ConstantToClassConstantBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated FILE_MODIFY_PERMISSIONS constant use.
 *
 * No change record found.
 *
 * What is covered:
 * - See `ConstantToClassConstantBase.php`
 *
 * Improvement opportunities
 *  - See `ConstantToClassConstantBase.php`
 */
final class FileModifyPermissionsRector extends ConstantToClassConstantBase
{
  protected $deprecatedConstant = 'FILE_MODIFY_PERMISSIONS';

  protected $constantFullyQualifiedClassName = 'Drupal\Core\File\FileSystemInterface';

  protected $constant = 'MODIFY_PERMISSIONS';


  /**
   * @inheritdoc
   */
  public function getDefinition(): RectorDefinition
  {
    return new RectorDefinition('Fixes deprecated FILE_MODIFY_PERMISSIONS use',[
      new CodeSample(
        <<<'CODE_BEFORE'
$result = file_prepare_directory($destination, FILE_MODIFY_PERMISSIONS);
CODE_BEFORE
        ,
        <<<'CODE_AFTER'
$result = file_prepare_directory($destination, \Drupal\Core\File\FileSystemInterface::MODIFY_PERMISSIONS);
CODE_AFTER
      )
    ]);
  }
}
