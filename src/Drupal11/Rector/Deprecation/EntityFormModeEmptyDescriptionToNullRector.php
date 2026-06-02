<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces an empty-string description with NULL in EntityFormMode::create() calls.
 *
 * Setting the description property of an EntityFormMode to '' was deprecated in
 * drupal:11.2.0 and must be NULL in drupal:12.0.0. The replacement is a plain
 * PHP value change, so no BC wrapping is needed.
 *
 * @see https://www.drupal.org/node/3448457
 * @see https://www.drupal.org/node/3452144
 */
class EntityFormModeEmptyDescriptionToNullRector extends AbstractRector
{
    // TODO PHPSTAN_MESSAGES EntityFormModeEmptyDescriptionToNullRector: PHPStan
    //   emits no deprecation for the targeted call. The deprecation is a
    //   runtime config-schema constraint validation against EntityFormMode's
    //   description property; it is not surfaced as a @deprecated annotation
    //   on EntityFormMode::create() or its parent's create(). No string is
    //   available to add here.

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace empty-string description with NULL in EntityFormMode::create() calls (deprecated in drupal:11.2.0).',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
EntityFormMode::create([
  'id' => 'user.test',
  'label' => 'Test',
  'description' => '',
  'targetEntityType' => 'user',
]);
CODE_BEFORE,
                    <<<'CODE_AFTER'
EntityFormMode::create([
  'id' => 'user.test',
  'label' => 'Test',
  'description' => NULL,
  'targetEntityType' => 'user',
]);
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param StaticCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, 'create')) {
            return null;
        }

        if (!$node->class instanceof Name) {
            return null;
        }

        // Static-call guard: match against the fully-qualified class name.
        if (!$this->isName($node->class, 'Drupal\Core\Entity\Entity\EntityFormMode')) {
            return null;
        }

        if (empty($node->args)) {
            return null;
        }

        $firstArg = $node->args[0];
        if (!$firstArg instanceof Arg) {
            return null;
        }

        if (!$firstArg->value instanceof Array_) {
            return null;
        }

        $changed = false;
        foreach ($firstArg->value->items as $item) {
            if (!$item->key instanceof String_) {
                continue;
            }

            if ($item->key->value !== 'description') {
                continue;
            }

            if (!$item->value instanceof String_) {
                continue;
            }

            if ($item->value->value !== '') {
                continue;
            }

            $item->value = new ConstFetch(new Name('NULL'));
            $changed = true;
        }

        return $changed ? $node : null;
    }
}
