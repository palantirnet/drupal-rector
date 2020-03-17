<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\TraitsByClassHelperTrait;
use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated getMock() calls.
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
            Node\Stmt\Expression::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Stmt\Expression $node */
        $expr = $node->expr;
        if ($expr instanceof Node\Expr\MethodCall && 'getMock' === (string) $expr->name) {

            // Get the calling variable.
            $var = (string) $expr->var->name;

            // Build the arguments.
            $method_arguments = [
                $node->expr->args[0],
            ];

            // Get the updated method name.
            $new_method = 'createMock';

            // Make the new.
            $method = new Node\Identifier($new_method);
            $expr = new Node\Expr\MethodCall($expr->var, $method, $method_arguments);
            $node = new Node\Stmt\Expression($expr);

        }

        return $node;
    }
}
