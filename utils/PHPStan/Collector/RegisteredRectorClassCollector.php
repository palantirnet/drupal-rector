<?php

declare(strict_types=1);

namespace DrupalRector\PHPStan\Collector;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * Collects the drupal-rector rule classes each config file registers, for
 * \DrupalRector\PHPStan\Rule\ComposerBasedSetCoverageRule to compare across
 * files.
 *
 * @implements Collector<ClassConstFetch, string|null>
 */
final class RegisteredRectorClassCollector implements Collector
{
    public function getNodeType(): string
    {
        return ClassConstFetch::class;
    }

    public function processNode(Node $node, Scope $scope): ?string
    {
        if (!$node->name instanceof Node\Identifier) {
            return null;
        }

        if ($node->name->toLowerString() !== 'class') {
            return null;
        }

        if (!$node->class instanceof Node\Name) {
            return null;
        }

        $className = $scope->resolveName($node->class);

        if (!str_starts_with($className, 'DrupalRector\\')) {
            return null;
        }

        if (!str_ends_with($className, 'Rector')) {
            return null;
        }

        return $className;
    }
}
