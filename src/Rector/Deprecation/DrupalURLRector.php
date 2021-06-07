<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated \Drupal::url() calls.
 *
 * There is no referenced change record for this. This may be related, https://www.drupal.org/node/2046643.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class DrupalURLRector extends AbstractRector
{

    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\StaticCall::class,
        ];
    }

  /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal::url() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
\Drupal::url('user.login');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal\Core\Url::fromRoute('user.login')->toString();
CODE_AFTER
            )
        ]);
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\StaticCall $node */
        if ($this->getName($node->name) === 'url' && $this->getName($node->class) === 'Drupal') {

            $toString_argument = NULL;
            $fromRoute_arguments = $node->args;

            // If we are the optional fourth argument, we need to chain a `toString($collect_bubbleable_metadata)`.
            if (count($fromRoute_arguments) === 4) {
               $toString_argument = $fromRoute_arguments[3];

               unset($fromRoute_arguments[3]);
            }

            $new_node = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal\Core\Url'), 'fromRoute', $fromRoute_arguments);

            if (is_null($toString_argument)) {
                $new_node = new Node\Expr\MethodCall($new_node, 'toString');
            }
            else {
                $new_node = new Node\Expr\MethodCall($new_node, 'toString', [$toString_argument]);
            }

            return $new_node;
        }

        return null;
    }
}
