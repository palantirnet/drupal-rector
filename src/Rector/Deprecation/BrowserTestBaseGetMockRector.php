<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\GetMockBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated getMock() calls in classes that extend BrowserTestBase.
 *
 * See https://www.drupal.org/node/2907725 for change record.
 *
 * What is covered:
 * - Calls from classes that extend BrowserTestBase
 */
final class BrowserTestBaseGetMockRector extends GetMockBase
{
    protected $baseClassBeingExtended = 'Drupal\Tests\BrowserTestBase';

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated getMock() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
$this->entityTypeManager = $this->getMock(EntityTypeManagerInterface::class);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
CODE_AFTER
            )
        ]);
    }
}
