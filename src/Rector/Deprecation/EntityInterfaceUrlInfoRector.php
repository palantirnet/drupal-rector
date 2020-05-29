<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\MethodToMethodBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated function call to EntityInterface::urlInfo.
 *
 * See https://www.drupal.org/node/2614344 for change record.
 *
 * What is covered:
 * - See MethodToMethodBase.php
 *
 * Improvement opportunities:
 * - See MethodToMethodBase.php
 */
final class EntityInterfaceUrlInfoRector extends MethodToMethodBase
{
    protected $deprecatedMethodName = 'urlInfo';

    protected $methodName = 'toUrl';

    protected $className = 'Drupal\Core\Entity\EntityInterface';

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated urlInfo() calls',[
          new CodeSample(
            <<<'CODE_BEFORE'
$url = $entity->urlInfo();
CODE_BEFORE
            ,
            <<<'CODE_AFTER'
$url = $entity->toUrl();
CODE_AFTER
        )
      ]);
    }
}
