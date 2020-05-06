<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated function call to EntityInterface::urlInfo.
 *
 * See https://www.drupal.org/node/2614344 for change record.
 *
 * What is covered:
 * - Checks the class being extended.
 *
 * Improvement opportunities:
 * - Check that the class is an entity.
 */
final class EntityInterfaceUrlInfoRector extends AbstractRector
{

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

    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\MethodCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\MethodCall $node */
        // TODO: Check the class to see if it implements EntityInterface.
        // I could not find any other methods called urlInfo in core and I'm not sure if there is a simple way to evaluate the class of the calling variable.
        if ($this->getName($node->name) === 'urlInfo') {
            $node->name = new Node\Name('toUrl');

            return $node;
        }

        return null;
    }
}
