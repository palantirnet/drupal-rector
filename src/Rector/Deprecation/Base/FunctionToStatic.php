<?php

namespace DrupalRector\Rector\Deprecation\Base;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

/**
 * Replaces function calls to static method calls.
 *
 * Example: \DrupalRector\Rector\Deprecation\FileDirectoryTempOsRector
 *
 * What is covered:
 * - Static replacement
 */
abstract class FunctionToStatic extends AbstractRector
{
    /**
     * Deprecated function name.
     *
     * Example: file_default_scheme
     *
     * @var string
     */
    protected $deprecatedFunctionName;

    /**
     * Replacement class name without trailing slash.
     *
     * Example: Drupal\Component\FileSystem\FileSystem
     *
     * @var string
     */
    protected $className;

    /**
     * Replacement method name
     *
     * Example: getOsTemporaryDirectory
     *
     * @var string
     */
    protected $methodName;

    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array {
        return [
            Node\Expr\FuncCall::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node {
        if ($this->getName($node) === $this->deprecatedFunctionName) {
            return new Node\Expr\StaticCall(new Node\Name\FullyQualified($this->className), $this->methodName);
        }
        return NULL;
    }


}
