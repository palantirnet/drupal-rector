<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes defineExtraOptions() overrides from Views HandlerBase subclasses.
 *
 * HandlerBase::defineExtraOptions() was deprecated in drupal:11.2.0, removed
 * in drupal:12.0.0, and has no replacement — Drupal core never called it,
 * so any override is dead code.
 *
 * @see https://www.drupal.org/node/3485084
 * @see https://www.drupal.org/node/3486781
 */
final class RemoveHandlerBaseDefineExtraOptionsRector extends AbstractRector
{
    private const HANDLER_BASE_FQCN = 'Drupal\views\Plugin\views\HandlerBase';

    private const PARENT_SHORT_NAMES = [
        'HandlerBase',
        'FieldHandlerBase',
        'FilterPluginBase',
        'SortPluginBase',
        'ArgumentPluginBase',
        'RelationshipPluginBase',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove overrides of the deprecated HandlerBase::defineExtraOptions() which has no replacement (drupal:11.2.0)',
            [
                new CodeSample(
                    'class MyFilter extends HandlerBase { public function defineExtraOptions(&$option) { $option[\'key\'] = []; } }',
                    'class MyFilter extends HandlerBase { }'
                ),
            ]
        );
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Class_);

        if (!$this->isHandlerBaseSubclass($node)) {
            return null;
        }

        $changed = false;
        foreach ($node->stmts as $key => $stmt) {
            if ($stmt instanceof ClassMethod && $this->isName($stmt, 'defineExtraOptions')) {
                unset($node->stmts[$key]);
                $changed = true;
            }
        }

        return $changed ? $node : null;
    }

    private function isHandlerBaseSubclass(Class_ $node): bool
    {
        if ($node->extends === null) {
            return false;
        }

        $parentName = $node->extends->toString();

        if ($parentName === self::HANDLER_BASE_FQCN) {
            return true;
        }

        foreach (self::PARENT_SHORT_NAMES as $short) {
            if ($parentName === $short || str_ends_with($parentName, '\\'.$short)) {
                return true;
            }
        }

        try {
            if ($this->isObjectType($node->extends, new ObjectType(self::HANDLER_BASE_FQCN))) {
                return true;
            }
        } catch (\Throwable) {
        }

        return false;
    }
}
