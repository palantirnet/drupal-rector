<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitor;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated KernelTestBase::installSchema('system', 'sequences') calls.
 *
 * The sequences table was deprecated in drupal:10.2.0 and removed in
 * drupal:12.0.0. Calls to KernelTestBase::installSchema() targeting this
 * table now throw a LogicException and must be removed. When 'sequences'
 * appears alongside other tables in an array form, only that entry is
 * removed.
 *
 * @see https://www.drupal.org/node/3335756
 * @see https://www.drupal.org/node/3349345
 */
class RemoveInstallSchemaSystemSequencesRector extends AbstractRector
{
    // TODO PHPSTAN_MESSAGES RemoveInstallSchemaSystemSequencesRector:
    // The installSchema() method itself is not annotated @deprecated — only
    // the specific ('system', 'sequences') argument combination triggers a
    // runtime trigger_error in core. PHPStan emits no static deprecation
    // message here, so upgrade_status cannot match this rector via the
    // standard $rector_covered lookup.
    public const PHPSTAN_MESSAGES = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            "Remove deprecated KernelTestBase::installSchema('system', 'sequences') calls; the sequences table was removed in Drupal 12.",
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
$this->installSchema('system', ['sequences']);
CODE_BEFORE,
                    <<<'CODE_AFTER'

CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Expression::class];
    }

    /** @param Expression $node */
    public function refactor(Node $node): int|Node|null
    {
        if (!$node->expr instanceof MethodCall) {
            return null;
        }

        $methodCall = $node->expr;

        if (!$this->isName($methodCall->name, 'installSchema')) {
            return null;
        }

        if (!$this->isObjectType($methodCall->var, new ObjectType('Drupal\KernelTests\KernelTestBase'))) {
            return null;
        }

        $args = $methodCall->args;
        if (count($args) < 2) {
            return null;
        }

        $firstArg = $args[0];
        if (!$firstArg instanceof Arg) {
            return null;
        }
        if (!$firstArg->value instanceof String_) {
            return null;
        }
        if ($firstArg->value->value !== 'system') {
            return null;
        }

        $secondArg = $args[1];
        if (!$secondArg instanceof Arg) {
            return null;
        }

        $tablesExpr = $secondArg->value;

        // Case 1: single string 'sequences' — remove the whole statement.
        if ($tablesExpr instanceof String_ && $tablesExpr->value === 'sequences') {
            return NodeVisitor::REMOVE_NODE;
        }

        // Case 2: array containing 'sequences'.
        if ($tablesExpr instanceof Array_) {
            $newItems = [];
            $foundSequences = false;

            foreach ($tablesExpr->items as $item) {
                if ($item->value instanceof String_ && $item->value->value === 'sequences') {
                    $foundSequences = true;
                    continue;
                }
                $newItems[] = $item;
            }

            if (!$foundSequences) {
                return null;
            }

            // Array is now empty — remove the whole statement.
            if (count($newItems) === 0) {
                return NodeVisitor::REMOVE_NODE;
            }

            $tablesExpr->items = $newItems;

            return $node;
        }

        return null;
    }
}
