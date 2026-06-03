<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Function_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Renames procedural {module}_requirements_alter() implementations to
 * {module}_runtime_requirements_alter().
 *
 * hook_requirements_alter() is deprecated in drupal:11.3.0 and removed in
 * drupal:13.0.0. The most common use case is altering status-report (runtime)
 * requirements, which maps directly to the new hook_runtime_requirements_alter().
 * Modules that also alter update-phase requirements must manually add a
 * {module}_update_requirements_alter() function.
 *
 * This is a NON-backward-compatible rewrite: hook_runtime_requirements_alter()
 * is only invoked on Drupal minors where it exists, so the renamed function is
 * never called on older Drupal (a silent no-op). It cannot be BC-wrapped — a
 * function declaration is not an Expr → Expr transformation, so DeprecationHelper
 * does not apply. The rule therefore lives in the opt-in DRUPAL_113_BREAKING set,
 * not the default deprecation set. Only apply it after dropping support for the
 * Drupal minors that predate hook_runtime_requirements_alter().
 *
 * @see https://www.drupal.org/node/3490846
 * @see https://www.drupal.org/node/3549685
 */
final class HookRequirementsAlterRenameRector extends AbstractRector
{
    private const SUFFIX = '_requirements_alter';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Rename {module}_requirements_alter() procedural hook implementations to {module}_runtime_requirements_alter() as required by the deprecation of hook_requirements_alter() in drupal:11.3.0.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
function mymodule_requirements_alter(array &$requirements): void {
  $requirements['php']['title'] = t('PHP version');
}
CODE_BEFORE,
                    <<<'CODE_AFTER'
function mymodule_runtime_requirements_alter(array &$requirements): void {
  $requirements['php']['title'] = t('PHP version');
}
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Function_::class];
    }

    /** @param Function_ $node */
    public function refactor(Node $node): ?Node
    {
        $name = $node->name->toString();

        // Exclude the API documentation function itself.
        if ($name === 'hook_requirements_alter') {
            return null;
        }

        // Match procedural implementations: {module_name}_requirements_alter.
        if (!str_ends_with($name, self::SUFFIX)) {
            return null;
        }

        // Skip the new hook names so the rule is idempotent and never clobbers
        // already-migrated implementations — both also end in _requirements_alter.
        if (str_ends_with($name, '_runtime_requirements_alter')
            || str_ends_with($name, '_update_requirements_alter')) {
            return null;
        }

        // Must have exactly one parameter (the $requirements array by reference).
        if (count($node->params) !== 1) {
            return null;
        }

        // The parameter must be passed by reference (array &$requirements).
        if (!$node->params[0]->byRef) {
            return null;
        }

        // Rename *_requirements_alter to *_runtime_requirements_alter.
        $prefix = substr($name, 0, -strlen(self::SUFFIX));
        $node->name = new Identifier($prefix.'_runtime_requirements_alter');

        return $node;
    }
}
