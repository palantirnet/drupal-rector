<?php

namespace DrupalRector\Rector\Deprecation;

use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

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
    protected $baseClassBeingExtended = 'BrowserTestBase';

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
