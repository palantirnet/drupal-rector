<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated string $values argument in BlockContentTestBase::createBlockContentType() with an array.
 *
 * Deprecated in drupal:11.1.0 and removed in drupal:12.0.0. Callers must now
 * pass an explicit array such as ['id' => 'basic'] instead of a plain string.
 *
 * @see https://www.drupal.org/node/3196937
 * @see https://www.drupal.org/node/3473739
 */
class BlockContentTestBaseStringToArrayRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            "Replace deprecated string \$values in BlockContentTestBase::createBlockContentType() with an ['id' => ...] array",
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
$this->createBlockContentType('basic', TRUE);
CODE_BEFORE,
                    <<<'CODE_AFTER'
$this->createBlockContentType(['id' => 'basic'], TRUE);
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /** @param MethodCall $node */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, 'createBlockContentType')) {
            return null;
        }

        if (count($node->args) === 0) {
            return null;
        }

        $firstArg = $node->args[0];
        if (!$firstArg instanceof Arg) {
            return null;
        }

        if (!$firstArg->value instanceof String_) {
            return null;
        }

        // Skip InlineBlockTestBase::createBlockContentType($id, $label) — two string args.
        if (isset($node->args[1])) {
            $secondArg = $node->args[1];
            if ($secondArg instanceof Arg && $secondArg->value instanceof String_) {
                return null;
            }
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Tests\block_content\Traits\BlockContentCreationTrait'))) {
            return null;
        }

        $firstArg->value = new Array_([new ArrayItem($firstArg->value, new String_('id'))]);

        return $node;
    }
}
