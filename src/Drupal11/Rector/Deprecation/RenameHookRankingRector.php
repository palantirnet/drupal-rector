<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Renames the deprecated OOP hook attribute #[Hook('ranking')] to
 * #[Hook('node_search_ranking')].
 *
 * hook_ranking() is deprecated in drupal:11.3.0 and removed in drupal:12.0.0;
 * use hook_node_search_ranking() instead. Any method decorated with
 * #[Hook('ranking')] from Drupal\Core\Hook\Attribute\Hook must be updated to
 * #[Hook('node_search_ranking')].
 *
 * This is a NON-backward-compatible rewrite: the node_search_ranking hook is
 * only invoked on Drupal minors where it exists, so on older Drupal the renamed
 * attribute is never invoked (a silent no-op). It cannot be BC-wrapped — an
 * Attribute is not an Expr → Expr transformation, so DeprecationHelper does not
 * apply. The rule therefore lives in the opt-in DRUPAL_113_BREAKING set, not the
 * default deprecation set. Only apply it after dropping support for the Drupal
 * minors that predate hook_node_search_ranking().
 *
 * The rule only changes the #[Hook] attribute argument. It does not rename the
 * implementing method itself (the method name is not constrained by the hook
 * system), nor does it update any @deprecated or @see docblock text.
 *
 * @see https://www.drupal.org/node/1019966
 * @see https://www.drupal.org/node/2690393
 */
final class RenameHookRankingRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Rename #[Hook(\'ranking\')] to #[Hook(\'node_search_ranking\')] following the deprecation of hook_ranking() in drupal:11.3.0.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
#[Hook('ranking')]
public function ranking(): array { return []; }
CODE_BEFORE,
                    <<<'CODE_AFTER'
#[Hook('node_search_ranking')]
public function ranking(): array { return []; }
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Attribute::class];
    }

    /** @param Attribute $node */
    public function refactor(Node $node): ?Node
    {
        // Only target \Drupal\Core\Hook\Attribute\Hook attributes.
        if ($this->getName($node->name) !== 'Drupal\\Core\\Hook\\Attribute\\Hook') {
            return null;
        }

        if ($node->args === []) {
            return null;
        }

        $firstArg = $node->args[0]->value;
        if (!$firstArg instanceof String_) {
            return null;
        }

        if ($firstArg->value !== 'ranking') {
            return null;
        }

        $firstArg->value = 'node_search_ranking';

        return $node;
    }
}
