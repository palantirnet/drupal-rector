<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToStatic;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated file_directory_temp() calls.
 *
 * See https://www.drupal.org/node/2802569 for change record.
 *
 * What is covered:
 * - Static replacement
 */
final class TaxonomyImplodeTagsRector extends FunctionToStatic
{
    protected $deprecatedFunctionName = 'taxonomy_implode_tags';

    protected $className = 'Drupal\Core\Entity\Element\EntityAutocomplete';

    protected $methodName = 'getEntityLabels';

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated taxonomy_implode_tags() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
$var = taxonomy_implode_tags();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$var = \Drupal\Core\Entity\Element\EntityAutocomplete::getEntityLabels();
CODE_AFTER
            )
        ]);
    }


}
