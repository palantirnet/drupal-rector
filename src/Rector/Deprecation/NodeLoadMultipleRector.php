<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\EntityLoadBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaced deprecated node_load_multiple() calls.
 *
 * See https://www.drupal.org/node/2266845 for change record.
 *
 * What is covered:
 * - See EntityLoadBase.php
 *
 * Improvement opportunities
 * - See EntityLoadBase.php
 */
final class NodeLoadMultipleRector extends EntityLoadBase
{
    protected $entityType = 'node';

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated node_load_multiple() use',[
            new CodeSample(
                <<<'CODE_BEFORE'
$nodes = node_load_multiple([123, 456]);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$nodes = \Drupal::entityManager()->getStorage('node')->loadMultiple([123, 456]);
CODE_AFTER
            )
        ]);
    }

}
