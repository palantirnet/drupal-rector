<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated postInstall() and postInstallTasks() method overrides in Updater subclasses.
 *
 * Deprecated in drupal:11.1.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3417136
 */
final class RemoveUpdaterPostInstallMethodsRector extends AbstractRector
{
    private const DEPRECATED_METHODS = ['postInstall', 'postInstallTasks'];

    private const UPDATER_BASE_CLASSES = [
        'Drupal\Core\Updater\Updater',
        'Drupal\Core\Updater\Module',
        'Drupal\Core\Updater\Theme',
    ];

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): mixed
    {
        assert($node instanceof Class_);

        if ($node->extends === null) {
            return null;
        }

        if (!in_array($node->extends->toString(), self::UPDATER_BASE_CLASSES, true)) {
            return null;
        }

        $modified = false;
        $newStmts = [];
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof ClassMethod
                && in_array($this->getName($stmt), self::DEPRECATED_METHODS, true)
            ) {
                $modified = true;
                continue;
            }
            $newStmts[] = $stmt;
        }

        if (!$modified) {
            return null;
        }

        $node->stmts = $newStmts;
        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove deprecated Updater::postInstall() and postInstallTasks() method overrides (drupal:11.1.0)', [
            new CodeSample(
                "class MyUpdater extends Updater { public function postInstallTasks() { return []; } }",
                "class MyUpdater extends Updater { }"
            ),
        ]);
    }
}
