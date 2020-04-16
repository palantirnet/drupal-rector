<?php

namespace DrupalRector\Rector\Deprecation;

use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated FILE_EXISTS_REPLACE constant use.
 *
 * See https://www.drupal.org/node/3006851 for change record.
 *
 * What is covered:
 * - Fully qualified class name replacement
 *
 * Improvement opportunities
 * - Add a use statement
 */
final class FileExistsReplaceRector extends ConstantToClassConstantBase
{
  protected $deprecatedConstant = 'FILE_EXISTS_REPLACE';

  protected $constantFullyQualifiedClassName = 'Drupal\Core\File\FileSystemInterface';

  protected $constant = 'EXISTS_REPLACE';


  /**
   * @inheritdoc
   */
  public function getDefinition(): RectorDefinition
  {
    return new RectorDefinition('Fixes deprecated FILE_EXISTS_REPLACE use',[
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
