<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\EntityLoadBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaced deprecated entity_load() calls.
 *
 * See https://www.drupal.org/node/2266845 for change record.
 *
 * What is covered:
 * - See EntityLoadBase.php
 *
 * Improvement opportunities
 * - See EntityLoadBase.php
 */
final class EntityLoadRector extends EntityLoadBase
{

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated entity_load() use',[
            new CodeSample(
                <<<'CODE_BEFORE'
$node = entity_load('node', 123);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$node = \Drupal::entityManager()->getStorage('node')->load(123);
CODE_AFTER
            )
        ]);
    }

}
