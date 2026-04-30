<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Converts PDO::FETCH_* constants to FetchAs enum cases in Drupal Database API.
 *
 * Deprecated in drupal:11.2.0 and removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3525077
 */
final class ReplacePdoFetchConstantsRector extends AbstractRector
{
    private const FETCH_MAP = [
        'FETCH_OBJ' => 'Object',
        'FETCH_ASSOC' => 'Associative',
        'FETCH_NUM' => 'List',
        'FETCH_COLUMN' => 'Column',
        'FETCH_CLASS' => 'ClassObject',
    ];

    private const DRUPAL_FETCH_METHODS = [
        'setFetchMode' => 0,
        'fetch' => 0,
        'fetchAll' => 0,
        'fetchAllAssoc' => 1,
    ];

    private const PDO_RETURN_METHODS = ['getClientStatement', 'getClientConnection'];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace PDO::FETCH_* constants with FetchAs enum cases in Drupal Database API calls',
            [
                new CodeSample(
                    '$statement->setFetchMode(\\PDO::FETCH_ASSOC);',
                    '$statement->setFetchMode(\\Drupal\\Core\\Database\\Statement\\FetchAs::Associative);'
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, ArrayItem::class];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof MethodCall) {
            return $this->refactorMethodCall($node);
        }
        if ($node instanceof ArrayItem) {
            return $this->refactorArrayItem($node);
        }

        return null;
    }

    private function refactorMethodCall(MethodCall $node): ?MethodCall
    {
        $methodName = $this->getName($node->name);
        if ($methodName === null || !array_key_exists($methodName, self::DRUPAL_FETCH_METHODS)) {
            return null;
        }

        if ($node->var instanceof MethodCall) {
            $calleeName = $this->getName($node->var->name);
            if ($calleeName !== null && in_array($calleeName, self::PDO_RETURN_METHODS, true)) {
                return null;
            }
        }

        $fetchArgIndex = self::DRUPAL_FETCH_METHODS[$methodName];
        $changed = false;

        foreach ($node->args as $index => $arg) {
            if (!$arg instanceof Arg || $index !== $fetchArgIndex) {
                continue;
            }
            $replacement = $this->replacePdoFetchConst($arg->value);
            if ($replacement !== null) {
                $arg->value = $replacement;
                $changed = true;
            }
        }

        return $changed ? $node : null;
    }

    private function refactorArrayItem(ArrayItem $node): ?ArrayItem
    {
        if ($node->key === null) {
            return null;
        }
        if (!($node->key instanceof String_) || $node->key->value !== 'fetch') {
            return null;
        }
        $replacement = $this->replacePdoFetchConst($node->value);
        if ($replacement === null) {
            return null;
        }
        $node->value = $replacement;

        return $node;
    }

    private function replacePdoFetchConst(Node $node): ?ClassConstFetch
    {
        if (!$node instanceof ClassConstFetch) {
            return null;
        }
        $className = $this->getName($node->class);
        if ($className !== 'PDO') {
            return null;
        }
        $constName = $this->getName($node->name);
        if ($constName === null || !isset(self::FETCH_MAP[$constName])) {
            return null;
        }

        return new ClassConstFetch(
            new FullyQualified('Drupal\Core\Database\Statement\FetchAs'),
            self::FETCH_MAP[$constName]
        );
    }
}
