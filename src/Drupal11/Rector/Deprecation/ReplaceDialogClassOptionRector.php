<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Rewrites the removed $dialog_options['dialogClass'] key to
 * $dialog_options['classes']['ui-dialog'] in OpenDialogCommand,
 * OpenModalDialogCommand, and OpenOffCanvasDialogCommand constructors.
 *
 * The 'dialogClass' option was deprecated in drupal:11.3.0 and removed in
 * drupal:12.0.0. Callers that pass a literal array to the $dialog_options
 * argument must use $dialog_options['classes']['ui-dialog'] instead. The
 * classes[ui-dialog] form has existed in core since 10.3.x, so the
 * transformed code is safe across all drupal-rector–supported Drupal minors
 * (D10.3+).
 *
 * OpenModalDialogCommand is covered because it extends OpenDialogCommand
 * and forwards its $dialog_options to parent::__construct(), so calls to it
 * with a literal ['dialogClass' => ...] array trigger the same deprecation
 * notice from the parent constructor.
 *
 * @see https://www.drupal.org/node/3571054
 * @see https://www.drupal.org/node/3440844
 */
final class ReplaceDialogClassOptionRector extends AbstractRector
{
    /**
     * Map: FQCN => zero-based index of the $dialog_options argument.
     */
    private const CLASS_ARG_INDEX = [
        'Drupal\\Core\\Ajax\\OpenDialogCommand' => 3,
        'Drupal\\Core\\Ajax\\OpenModalDialogCommand' => 2,
        'Drupal\\Core\\Ajax\\OpenOffCanvasDialogCommand' => 2,
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            "Replace removed \$dialog_options['dialogClass'] with \$dialog_options['classes']['ui-dialog'] in OpenDialogCommand / OpenModalDialogCommand / OpenOffCanvasDialogCommand constructors (removed in drupal:12.0.0)",
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
new \Drupal\Core\Ajax\OpenDialogCommand('#my-dialog', 'Title', $content, ['dialogClass' => 'my-class', 'width' => 600]);
CODE_BEFORE,
                    <<<'CODE_AFTER'
new \Drupal\Core\Ajax\OpenDialogCommand('#my-dialog', 'Title', $content, ['width' => 600, 'classes' => ['ui-dialog' => 'my-class']]);
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    /** @param New_ $node */
    public function refactor(Node $node): ?Node
    {
        $className = $this->getResolvedClassName($node);
        if ($className === null || !isset(self::CLASS_ARG_INDEX[$className])) {
            return null;
        }

        $argIndex = self::CLASS_ARG_INDEX[$className];

        if (!isset($node->args[$argIndex])) {
            return null;
        }

        $arg = $node->args[$argIndex];
        if (!$arg instanceof Node\Arg) {
            return null;
        }

        $optionsArray = $arg->value;
        if (!$optionsArray instanceof Array_) {
            return null;
        }

        // Phase 1: locate the items and hold references (not indexes — node
        // references survive the later array_values() reindex).
        $dialogClassIdx = null;
        $dialogClassValue = null;
        $dialogClassMatches = 0;
        $classesItem = null;
        $uiDialogItem = null;

        foreach ($optionsArray->items as $idx => $item) {
            if (!$item->key instanceof String_) {
                continue;
            }

            if ($item->key->value === 'dialogClass') {
                $dialogClassIdx = $idx;
                $dialogClassValue = $item->value;
                ++$dialogClassMatches;
            } elseif ($item->key->value === 'classes') {
                $classesItem = $item;
                if ($item->value instanceof Array_) {
                    foreach ($item->value->items as $subItem) {
                        if ($subItem->key instanceof String_
                            && $subItem->key->value === 'ui-dialog'
                        ) {
                            $uiDialogItem = $subItem;
                        }
                    }
                }
            }
        }

        if ($dialogClassIdx === null) {
            return null;
        }

        // Bail on pathological duplicate `dialogClass` keys — picking one
        // would leave the other in place, leaving the deprecated key behind.
        if ($dialogClassMatches > 1) {
            return null;
        }

        // Phase 2: validate that the chosen merge branch is safe to execute.
        // Done before any mutation so a failed check never leaves a
        // half-rewritten array (deprecated key removed, replacement skipped).
        // Capture narrowed values into typed locals — phase 3 reads from
        // these so type narrowing survives the intervening mutation.
        $classesInnerArray = null;
        $concatenatedString = null;

        if ($classesItem !== null) {
            // Merging into existing `classes` requires it to resolve to a
            // literal array node — we cannot safely insert into a variable
            // or function-call result.
            if (!$classesItem->value instanceof Array_) {
                return null;
            }
            $classesInnerArray = $classesItem->value;

            if ($uiDialogItem !== null) {
                // Concatenation path: both the existing ui-dialog value and
                // the new dialogClass value must be string literals.
                if (!$uiDialogItem->value instanceof String_
                    || !$dialogClassValue instanceof String_
                ) {
                    return null;
                }
                $concatenatedString = $uiDialogItem->value->value.' '.$dialogClassValue->value;
            }
        }

        // Phase 3: mutate. All checks above passed; this branch is committed.
        unset($optionsArray->items[$dialogClassIdx]);
        $optionsArray->items = array_values($optionsArray->items);

        if ($classesItem === null) {
            // Branch A: no `classes` key existed — add a fresh one.
            $optionsArray->items[] = new ArrayItem(
                new Array_([
                    new ArrayItem($dialogClassValue, new String_('ui-dialog')),
                ]),
                new String_('classes')
            );
        } elseif ($uiDialogItem !== null && $concatenatedString !== null) {
            // Branch B: `classes['ui-dialog']` already present — concatenate.
            $uiDialogItem->value = new String_($concatenatedString);
        } elseif ($classesInnerArray !== null) {
            // Branch C: `classes` exists but has no `ui-dialog` entry — append.
            $classesInnerArray->items[] = new ArrayItem(
                $dialogClassValue,
                new String_('ui-dialog')
            );
        }

        return $node;
    }

    private function getResolvedClassName(New_ $node): ?string
    {
        $class = $node->class;

        if ($class instanceof FullyQualified) {
            return $class->toString();
        }

        if ($class instanceof Node\Name) {
            $resolved = $class->getAttribute('resolvedName');
            if ($resolved instanceof FullyQualified) {
                return $resolved->toString();
            }
        }

        return null;
    }
}
