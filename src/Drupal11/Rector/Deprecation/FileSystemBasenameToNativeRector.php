<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated FileSystemInterface::basename() with native basename().
 *
 * FileSystemInterface::basename() is deprecated in drupal:11.3.0 and removed
 * in drupal:13.0.0. PHP native basename() is identical on PHP 8.x+.
 *
 * @see https://www.drupal.org/node/3530461
 */
final class FileSystemBasenameToNativeRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated FileSystemInterface::basename() calls with PHP native basename()',
            [
                new CodeSample(
                    '$fileSystem->basename($uri, $suffix);',
                    'basename($uri, $suffix);'
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, 'basename')) {
            return null;
        }

        $callerType = $this->getType($node->var);
        $isFileSystem = false;
        foreach (['Drupal\Core\File\FileSystemInterface', 'Drupal\Core\File\FileSystem'] as $class) {
            if ($this->isObjectType($node->var, new ObjectType($class))) {
                $isFileSystem = true;
                break;
            }
        }

        if (!$isFileSystem) {
            return null;
        }

        return new FuncCall(new Name('basename'), $node->getArgs());
    }
}
