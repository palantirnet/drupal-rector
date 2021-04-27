<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\EntityViewBase;
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
final class EntityViewRector extends EntityViewBase
{

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated entity_view() use',[
            new CodeSample(
                <<<'CODE_BEFORE'
$rendered = entity_view($entity, 'default');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$rendered = \Drupal::entityTypeManager()->getViewBuilder($entity
  ->getEntityTypeId())->view($entity, 'default');
CODE_AFTER
            )
        ]);
    }

}
