<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces `new NodeViewController(...)` with `new EntityViewController(...)`,
 * dropping the extra $current_user and $entity_repository constructor
 * arguments that EntityViewController does not accept.
 *
 * This is the instantiation half of the NodeViewController removal. A
 * RenameClassRector entry (registered alongside this rule in
 * `config/drupal-11/drupal-11.4-breaking.php`) handles the structural
 * references: `use` imports, type hints, `::class`, and `extends`
 * declarations.
 *
 * BREAKING: this rule lives in the opt-in DRUPAL_114_BREAKING set, not the
 * default deprecation set. EntityViewController exists on every supported
 * Drupal minor, so the rewritten instantiation never fatals on a missing
 * symbol. The break is *behavioral* and is carried by the companion
 * RenameClassRector pass: reparenting a `class X extends NodeViewController`
 * to `extends EntityViewController` loses NodeViewController's node-specific
 * overrides on every minor — its 4-service `create()`, the `currentUser` /
 * `entityRepository` properties, the `title()` callback, and the node
 * `view()` signature. A subclass that relied on those can throw an
 * ArgumentCountError (inherited 2-arg `create()` vs a 4-arg constructor) or
 * call an undefined `title()`. Those require manual review, hence opt-in.
 *
 * This rule matches both the old (`Drupal\node\Controller\NodeViewController`)
 * and the new (`Drupal\Core\Entity\Controller\EntityViewController`) class
 * names so the argument trim is correct regardless of whether the companion
 * RenameClassRector has already rewritten the `new` expression's class name.
 *
 * Caveat: a subclass that overrides __construct and calls
 * `parent::__construct($a, $b, $c, $d)` keeps all four arguments — PHP
 * silently discards the extras, so $current_user / $entity_repository are no
 * longer forwarded. Those parent calls are not rewritten.
 *
 * @see https://www.drupal.org/node/3589630
 * @see https://www.drupal.org/node/3589636
 */
class ReplaceNodeViewControllerRector extends AbstractRector
{
    private const OLD_CLASS = 'Drupal\node\Controller\NodeViewController';

    private const NEW_CLASS = 'Drupal\Core\Entity\Controller\EntityViewController';

    // NodeViewController is annotated `@deprecated in drupal:11.4.0` at the
    // class level, so phpstan-deprecation-rules flags both instantiation and
    // `extends`. The verbatim strings below follow the standard
    // phpstan/phpstan-deprecation-rules format; verify against a real call
    // site with the rector-extract-phpstan-error skill before relying on them
    // for upgrade_status matching.
    public const PHPSTAN_MESSAGES = [
        'Instantiation of deprecated class Drupal\node\Controller\NodeViewController: in drupal:11.4.0 and is removed from drupal:13.0.0. Use \Drupal\Core\Entity\Controller\EntityViewController instead.',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace new NodeViewController(...) with new EntityViewController(...) and drop the extra constructor arguments.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
new \Drupal\node\Controller\NodeViewController($entityTypeManager, $renderer, $currentUser, $entityRepository);
CODE_BEFORE,
                    <<<'CODE_AFTER'
new \Drupal\Core\Entity\Controller\EntityViewController($entityTypeManager, $renderer);
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

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof New_);
        if (!$node->class instanceof Name) {
            return null;
        }

        $isOldClass = $this->isName($node->class, self::OLD_CLASS);
        $isNewClass = $this->isName($node->class, self::NEW_CLASS);
        if (!$isOldClass && !$isNewClass) {
            return null;
        }

        $changed = false;

        if ($isOldClass) {
            $node->class = new FullyQualified(self::NEW_CLASS);
            $changed = true;
        }

        // EntityViewController::__construct only accepts ($entity_type_manager,
        // $renderer); drop any extra arguments carried over from
        // NodeViewController's 4-argument constructor.
        if (count($node->args) > 2) {
            $node->args = array_slice($node->args, 0, 2);
            $changed = true;
        }

        return $changed ? $node : null;
    }
}
