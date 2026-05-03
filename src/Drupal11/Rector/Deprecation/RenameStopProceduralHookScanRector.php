<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\UseUse;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Renames #[StopProceduralHookScan] attribute to #[ProceduralHookScanStop] and updates its use statement.
 *
 * Deprecated in drupal:11.2.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3495943
 */
final class RenameStopProceduralHookScanRector extends AbstractRector
{
    private const OLD_FQCN = 'Drupal\Core\Hook\Attribute\StopProceduralHookScan';
    private const NEW_FQCN = 'Drupal\Core\Hook\Attribute\ProceduralHookScanStop';
    private const NEW_SHORT = 'ProceduralHookScanStop';

    public function getNodeTypes(): array
    {
        return [UseUse::class, Attribute::class];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof UseUse) {
            if ($node->name->toString() === self::OLD_FQCN) {
                $node->name = new Name(explode('\\', self::NEW_FQCN));

                return $node;
            }

            return null;
        }

        assert($node instanceof Attribute);

        if ($node->name instanceof FullyQualified && $node->name->toString() === self::OLD_FQCN) {
            $node->name = new Name(self::NEW_SHORT);

            return $node;
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Rename #[StopProceduralHookScan] attribute to #[ProceduralHookScanStop] and update its use statement (drupal:11.2.0)', [
            new CodeSample(
                "use Drupal\\Core\\Hook\\Attribute\\StopProceduralHookScan;\n\n#[StopProceduralHookScan]\nfunction mymodule_helper(): void {}",
                "use Drupal\\Core\\Hook\\Attribute\\ProceduralHookScanStop;\n\n#[ProceduralHookScanStop]\nfunction mymodule_helper(): void {}"
            ),
        ]);
    }
}
