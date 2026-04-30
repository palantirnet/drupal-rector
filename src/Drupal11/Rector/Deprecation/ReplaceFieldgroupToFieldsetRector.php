<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces '#type' => 'fieldgroup' with '#type' => 'fieldset' in render arrays.
 *
 * The Fieldgroup render element is deprecated in drupal:11.2.0 and removed in
 * drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3512254
 */
final class ReplaceFieldgroupToFieldsetRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            "Replace '#type' => 'fieldgroup' with '#type' => 'fieldset' in render arrays",
            [
                new CodeSample(
                    "\$form['account'] = ['#type' => 'fieldgroup', '#title' => \$this->t('Account settings')];",
                    "\$form['account'] = ['#type' => 'fieldset', '#title' => \$this->t('Account settings')];"
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Array_::class];
    }

    public function refactor(Node $node): ?Node
    {
        $changed = false;
        foreach ($node->items as $item) {
            if ($item === null) {
                continue;
            }
            if (!$item->key instanceof String_ || $item->key->value !== '#type') {
                continue;
            }
            if (!$item->value instanceof String_ || $item->value->value !== 'fieldgroup') {
                continue;
            }
            $item->value = new String_('fieldset');
            $changed = true;
        }

        return $changed ? $node : null;
    }
}
