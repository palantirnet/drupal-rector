<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated entity_get_form_display() calls.
 *
 * See https://www.drupal.org/node/2835616 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class EntityGetFormDisplayRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'entity_get_form_display';

    protected $serviceName = 'entity_display.repository';

    protected $serviceMethodName = 'getFormDisplay';


    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated entity_get_form_display() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$display = entity_get_form_display($entity_type, $bundle, $form_mode)
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$display = \Drupal::service('entity_display.repository')
    ->getFormDisplay($entity_type, $bundle, $form_mode);
CODE_AFTER
            )
        ]);
    }
}
