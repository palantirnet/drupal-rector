<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces integer #access values in render arrays with proper booleans.
 *
 * Drupal deprecated passing non-boolean, non-AccessResultInterface values to
 * the #access render array key in drupal:11.4.0. This rule targets the most
 * common static case: integer literals. It converts non-zero integers to true
 * and 0 to false, leaving booleans, variables, and AccessResultInterface
 * expressions untouched.
 *
 * @see https://www.drupal.org/node/3526250
 * @see https://www.drupal.org/node/3549344
 */
class ReplaceNonBoolAccessRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace integer #access values with proper booleans in render arrays.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
$build = ['#markup' => 'foo', '#access' => 1];
CODE_BEFORE,
                    <<<'CODE_AFTER'
$build = ['#markup' => 'foo', '#access' => true];
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [ArrayItem::class];
    }

    /** @param ArrayItem $node */
    public function refactor(Node $node): ?Node
    {
        if ($node->key === null) {
            return null;
        }
        if (!$node->key instanceof String_) {
            return null;
        }
        if ($node->key->value !== '#access') {
            return null;
        }
        // Only act on integer literals; leave booleans, variables, and
        // AccessResultInterface expressions untouched.
        if (!$node->value instanceof Int_) {
            return null;
        }
        $node->value = $node->value->value === 0
            ? $this->nodeFactory->createFalse()
            : $this->nodeFactory->createTrue();

        return $node;
    }
}
