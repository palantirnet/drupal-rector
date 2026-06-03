<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeVisitor;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces $variables['page'] reads with $variables['view_mode'] === 'full'
 * in taxonomy term preprocess hooks.
 *
 * The $variables['page'] variable in taxonomy term templates is deprecated
 * in Drupal 11.3.0 and removed in 13.0.0. Use view_mode === 'full' instead.
 * Assignment targets are left untouched so initialisation in legacy code
 * is preserved.
 *
 * @see https://www.drupal.org/node/3535439
 * @see https://www.drupal.org/node/3542527
 */
final class TaxonomyTermPageVariableToViewModeRector extends AbstractRector
{
    // TODO PHPSTAN_MESSAGES TaxonomyTermPageVariableToViewModeRector:
    // The deprecation is signalled at runtime by core via
    // $variables['deprecations']['page'] (read by Twig); there is no
    // @deprecated PHP symbol annotation, so PHPStan emits no static error
    // for $variables['page'] reads. This rector is therefore not covered
    // by upgrade_status's isRectorCovered() lookup.

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            "Replace deprecated \$variables['page'] with \$variables['view_mode'] === 'full' in taxonomy term preprocess hooks.",
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
function mymodule_preprocess_taxonomy_term(array &$variables): void {
  if ($variables['page']) {
    $variables['show_title'] = FALSE;
  }
}
CODE_BEFORE,
                    <<<'CODE_AFTER'
function mymodule_preprocess_taxonomy_term(array &$variables): void {
  if ($variables['view_mode'] === 'full') {
    $variables['show_title'] = FALSE;
  }
}
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Function_::class, ClassMethod::class];
    }

    /**
     * @param Function_|ClassMethod $node
     */
    public function refactor(Node $node): ?Node
    {
        $name = $this->getName($node);
        if ($name === null) {
            return null;
        }

        $isPreprocessFunction = str_contains($name, 'preprocess_taxonomy_term')
            || str_contains($name, 'preprocessTaxonomyTerm');

        if (!$isPreprocessFunction) {
            return null;
        }

        $hasChanged = false;

        $this->traverseNodesWithCallable(
            (array) $node->stmts,
            function (Node $subNode) use (&$hasChanged) {
                if ($subNode instanceof Assign && $this->isPageVariablesArrayDimFetch($subNode->var)) {
                    return NodeVisitor::DONT_TRAVERSE_CHILDREN;
                }

                if (!$subNode instanceof ArrayDimFetch) {
                    return null;
                }

                if (!$this->isPageVariablesArrayDimFetch($subNode)) {
                    return null;
                }

                $hasChanged = true;

                return new Identical(
                    new ArrayDimFetch(
                        new Variable('variables'),
                        new String_('view_mode')
                    ),
                    new String_('full')
                );
            }
        );

        if (!$hasChanged) {
            return null;
        }

        return $node;
    }

    private function isPageVariablesArrayDimFetch(Node $node): bool
    {
        if (!$node instanceof ArrayDimFetch) {
            return false;
        }
        if (!$this->isName($node->var, 'variables')) {
            return false;
        }

        return $node->dim instanceof String_ && $node->dim->value === 'page';
    }
}
