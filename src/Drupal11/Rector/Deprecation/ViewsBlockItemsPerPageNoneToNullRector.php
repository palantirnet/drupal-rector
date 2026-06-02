<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces $block->setConfigurationValue('items_per_page', 'none') with NULL for Views block plugins.
 *
 * The string 'none' was deprecated in drupal:11.2.0 and removed in drupal:12.0.0;
 * NULL is the canonical value to inherit the items-per-page setting from the view.
 * The transformed code is safe to run on all Drupal versions, so no BC wrapping is required.
 *
 * @see https://www.drupal.org/node/3520946
 * @see https://www.drupal.org/node/3522240
 */
final class ViewsBlockItemsPerPageNoneToNullRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            "Replace \$block->setConfigurationValue('items_per_page', 'none') with NULL for Views block plugins.",
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
$block->setConfigurationValue('items_per_page', 'none');
CODE_BEFORE,
                    <<<'CODE_AFTER'
$block->setConfigurationValue('items_per_page', NULL);
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
        if (!$this->isName($node->name, 'setConfigurationValue')) {
            return null;
        }

        if (count($node->args) < 2) {
            return null;
        }

        $firstArg = $node->args[0];
        if (!$firstArg instanceof Arg || !$firstArg->value instanceof String_) {
            return null;
        }
        if ($firstArg->value->value !== 'items_per_page') {
            return null;
        }

        $secondArg = $node->args[1];
        if (!$secondArg instanceof Arg || !$secondArg->value instanceof String_) {
            return null;
        }
        if ($secondArg->value->value !== 'none') {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\views\Plugin\Block\ViewsBlockBase'))) {
            return null;
        }

        $node->args[1] = new Arg(new ConstFetch(new Node\Name('NULL')));

        return $node;
    }
}
