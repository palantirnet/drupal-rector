<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\MethodToMethodBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated function call to EntityType::getLowercaseLabel().
 *
 * See https://www.drupal.org/node/3075567 for change record.
 *
 * What is covered:
 * - Changes the name of the method.
 *
 */
final class EntityTypeGetLowercaseLabelRector extends MethodToMethodBase
{

  /**
   * Deprecated method name.
   *
   * @var string
   */
  protected $deprecatedMethodName = 'getLowercaseLabel';

  /**
   * The replacement method name.
   *
   * @var string
   */
  protected $methodName = 'getSingularLabel';

  /**
   * The type of class the method is being called on.
   *
   * @var string
   */
  protected $className = '\Drupal\Core\Entity\EntityType';

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated EntityType::getLowercaseLabel()',[
            new CodeSample(
                <<<'CODE_BEFORE'
/* @var \Drupal\node\Entity\Node $node */
$node = \Drupal::entityTypeManager()->getStorage('node')->load(123);
$entity_type = $node->getEntityType();
$entity_type->getLowercaseLabel();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
/* @var \Drupal\node\Entity\Node $node */
$node = \Drupal::entityTypeManager()->getStorage('node')->load(123);
$entity_type = $node->getEntityType();
$entity_type->getSingularLabel();
CODE_AFTER
        )
      ]);
    }

}
