<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\TraitsByClassHelperTrait;
use PhpParser\Node;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated getMock() calls.
 *
 * What is covered:
 * - All known cases (see BrowserTestBaseGetMock in rector_examples)
 *
 * See https://www.drupal.org/node/2907725 for change record.
 */
final class GetMockRector extends AbstractRector
{
    use TraitsByClassHelperTrait;

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
        $class_name = $node->getAttribute(AttributeKey::CLASS_NAME);

        /* @var Node\Expr\MethodCall $node */
        if ($this->getName($node) === 'getMock' && $this->getName($node->var) === 'this' && $class_name && isset($node->getAttribute('classNode')->extends->parts) && in_array('BrowserTestBase', $node->getAttribute('classNode')->extends->parts)) {

                // Build the arguments.
            $method_arguments = $node->args;

            // Get the updated method name.
            $method_name = new Node\Identifier('createMock');

            $node = new Node\Expr\MethodCall($node->var, $method_name, $method_arguments, $node->getAttributes());
        }

        return $node;
    }
}
