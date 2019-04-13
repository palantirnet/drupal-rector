<?php
/**
 * Copyright 2018 Google Inc.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */

namespace Drupal8Rector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\ConfiguredCodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces constants with class constants.
 */
class ConstToClassConstFetchRector extends AbstractRector
{
    /**
     * @var string[]
     */
    private $constClassConstMap;

    /**
     * ConstToClassConstFetchRector constructor.
     *
     * @param string[] $constClassConstMap
     *   Associative array where keys are constants and values are the new
     *   class constants that should be used instead.
     */
    public function __construct(array $constClassConstMap = [])
    {
        $this->constClassConstMap = $constClassConstMap;
    }

    /**
     * @inheritDoc
     */
    public function getNodeTypes(): array
    {
        return [
           Node\Expr\ConstFetch::class,
           Node\Stmt\Return_::class,
           Node\Stmt\Expression::class,
           Node\Expr\Assign::class,
       ];
    }

    /**
     * @inheritDoc
     */
    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Node\Expr\ConstFetch) {
            $constName = (string) $node->name;
            if (array_key_exists($constName, $this->constClassConstMap)) {
                list($fqcn, $classConstName) = explode('::', $this->constClassConstMap[$constName]);
                $node = new Node\Expr\ClassConstFetch(new Node\Name\FullyQualified($fqcn), $classConstName);
            }
        } elseif ($node instanceof Node\Stmt\Return_ && null !== $node->expr) {
            $node->expr = $this->refactor($node->expr);
        } elseif ($node instanceof Node\Expr\Assign) {
            $node->expr = $this->refactor($node->expr);
        } elseif ($node instanceof Node\Stmt\Expression) {
            $node->expr = $this->refactor($node->expr);
        }

        return $node;
    }

    /**
     * @inheritDoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Replaces constants with class constants.', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
$a = FILE_EXISTS_REPLACE;
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$a = Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE;
CODE_SAMPLE
                ,
                [
                    '$constClassConstMap' => [
                        'FILE_EXISTS_REPLACE' => 'Drupal\Core\File\FileSystemInterface::EXISTS_REPLACE',
                    ],
                ]
            ), ]
        );
    }
}
