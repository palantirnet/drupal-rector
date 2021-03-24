<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\ConstantToClassConstantBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

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
  public function getRuleDefinition(): RuleDefinition
  {
    return new RuleDefinition('Fixes deprecated FILE_MODIFY_PERMISSIONS use',[
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
