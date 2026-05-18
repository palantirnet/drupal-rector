<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replace deprecated check_markup() calls with a processed_text render array.
 *
 * @see https://www.drupal.org/node/455724
 * @see https://www.drupal.org/node/3588040
 */
class CheckMarkupToProcessedTextRector extends AbstractRector
{
    private const PARAM_MAP = [
        'text' => '#text',
        'format_id' => '#format',
        'langcode' => '#langcode',
        'filter_types_to_skip' => '#filter_types_to_skip',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated check_markup() calls with a processed_text render array.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
check_markup($text, $format_id);
CODE_BEFORE,
                    <<<'CODE_AFTER'
['#type' => 'processed_text', '#text' => $text, '#format' => $format_id];
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof FuncCall);

        if (!$this->isName($node->name, 'check_markup')) {
            return null;
        }

        $args = $node->args;
        if (count($args) === 0) {
            return null;
        }

        $items = [
            new ArrayItem(new String_('processed_text'), new String_('#type')),
        ];

        $paramNames = array_keys(self::PARAM_MAP);
        $positionalIndex = 0;

        foreach ($args as $arg) {
            if (!$arg instanceof Arg) {
                continue;
            }

            if ($arg->name !== null) {
                $paramName = $arg->name->toString();
                if (isset(self::PARAM_MAP[$paramName])) {
                    $items[] = new ArrayItem($arg->value, new String_(self::PARAM_MAP[$paramName]));
                }
            } else {
                if (isset($paramNames[$positionalIndex])) {
                    $key = self::PARAM_MAP[$paramNames[$positionalIndex]];
                    $items[] = new ArrayItem($arg->value, new String_($key));
                }

                ++$positionalIndex;
            }
        }

        return new Array_($items);
    }
}
