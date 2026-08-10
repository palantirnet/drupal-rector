<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use Rector\Rector\AbstractRector;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated LinkWidget::validateTitleElement() calls.
 *
 * Deprecated in drupal:11.4.0, removed in drupal:12.0.0.
 * Validation is now handled by LinkTitleRequiredConstraint on the LinkItem field type.
 *
 * @see https://www.drupal.org/node/3093118
 * @see https://www.drupal.org/node/3554139
 */
final class RemoveLinkWidgetValidateTitleElementRector extends AbstractRector implements ComposerPackageConstraintInterface, DocumentedRuleInterface
{
    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('drupal/core', '>=11.4.0');
    }

    public function getNodeTypes(): array
    {
        return [Node\Stmt\Expression::class];
    }

    /** @return NodeVisitor::REMOVE_NODE|null */
    public function refactor(Node $node): mixed
    {
        assert($node instanceof Node\Stmt\Expression);

        if (!$node->expr instanceof Node\Expr\StaticCall) {
            return null;
        }

        $staticCall = $node->expr;

        if (!$this->isName($staticCall->name, 'validateTitleElement')) {
            return null;
        }

        if (!$this->isName($staticCall->class, 'Drupal\link\Plugin\Field\FieldWidget\LinkWidget')) {
            return null;
        }

        return NodeVisitor::REMOVE_NODE;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Removes deprecated LinkWidget::validateTitleElement() calls. Validation is now handled by LinkTitleRequiredConstraint on the LinkItem field type (drupal:11.4.0)', [
            new CodeSample(
                <<<'CODE_BEFORE'
LinkWidget::validateTitleElement($element, $form_state, $form);
$element['#value'] = $value;
CODE_BEFORE,
                <<<'CODE_AFTER'
$element['#value'] = $value;
CODE_AFTER
            ),
        ]);
    }
}
