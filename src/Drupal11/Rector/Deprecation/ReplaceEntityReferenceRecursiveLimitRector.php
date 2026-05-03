<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Scalar\Int_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT with literal 20.
 *
 * Deprecated in drupal:11.4.0, removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3316878
 */
final class ReplaceEntityReferenceRecursiveLimitRector extends AbstractRector
{
    private const TARGET_CLASSES = [
        'EntityReferenceEntityFormatter',
        'Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter',
    ];

    public function getNodeTypes(): array
    {
        return [ClassConstFetch::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof ClassConstFetch);

        if (!$this->isName($node->name, 'RECURSIVE_RENDER_LIMIT')) {
            return null;
        }

        foreach (self::TARGET_CLASSES as $class) {
            if ($this->isName($node->class, $class)) {
                return new Int_(20);
            }
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT with literal 20 (drupal:11.4.0)', [
            new CodeSample(
                'if ($count > EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT) {}',
                'if ($count > 20) {}'
            ),
        ]);
    }
}
