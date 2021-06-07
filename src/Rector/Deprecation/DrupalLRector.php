<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated \Drupal::l() calls.
 *
 * See https://www.drupal.org/node/2346779 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class DrupalLRector extends AbstractRector
{

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal::l() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
\Drupal::l('User Login', \Drupal\Core\Url::fromRoute('user.login'));
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal\Core\Link::fromTextAndUrl('User Login', \Drupal\Core\Url::fromRoute('user.login'));
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
           Node\Expr\StaticCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\StaticCall $node */
        if ($this->getName($node->name) === 'l' && $this->getName($node->class) === 'Drupal') {
            $new_node = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal\Core\Link'), 'fromTextAndUrl', $node->args);

            return $new_node;
        }

        return null;
      }
}
