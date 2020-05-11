<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\EntityLoadBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaced deprecated file_load() calls.
 *
 * See https://www.drupal.org/node/2266845 for change record.
 *
 * What is covered:
 * - See EntityLoadBase.php
 *
 * Improvement opportunities
 * - See EntityLoadBase.php
 */
final class FileLoadRector extends EntityLoadBase
{
    protected $entityType = 'file';

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated file_load() use',[
            new CodeSample(
                <<<'CODE_BEFORE'
$file = file_load(123);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$file = \Drupal::entityManager()->getStorage('file')->load(123);
CODE_AFTER
            )
        ]);
    }

}
