<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\ConstantToClassConstantBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated FILE_CREATE_DIRECTORY constant use.
 *
 * No change record found.
 *
 * What is covered:
 * - See `ConstantToClassConstantBase.php`
 *
 * Improvement opportunities
 *  - See `ConstantToClassConstantBase.php`
 */
final class FileCreateDirectoryRector extends ConstantToClassConstantBase
{
    protected $deprecatedConstant = 'FILE_CREATE_DIRECTORY';

    protected $constantFullyQualifiedClassName = 'Drupal\Core\File\FileSystemInterface';

    protected $constant = 'CREATE_DIRECTORY';


    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated FILE_CREATE_DIRECTORY use',[
            new CodeSample(
              <<<'CODE_BEFORE'
$result = \Drupal::service('file_system')->prepareDirectory($directory, FILE_CREATE_DIRECTORY);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$result = \Drupal::service('file_system')->prepareDirectory($directory, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);
CODE_AFTER
            )
        ]);
    }
}
