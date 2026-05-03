<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\NodeVisitor;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated NodeStorage::revisionIds() and userRevisionIds() calls.
 *
 * @see https://www.drupal.org/node/3396062
 */
final class NodeStorageDeprecatedMethodsRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Expr\MethodCall::class, Node\Stmt\Expression::class];
    }

    /** @return Node|NodeVisitor::REMOVE_NODE|null */
    public function refactor(Node $node): mixed
    {
        if ($node instanceof Node\Stmt\Expression) {
            if (!$node->expr instanceof Node\Expr\MethodCall) {
                return null;
            }
            $methodCall = $node->expr;
            if ($this->getName($methodCall->name) !== 'countDefaultLanguageRevisions') {
                return null;
            }
            if (!$this->isObjectType($methodCall->var, new ObjectType('Drupal\node\NodeStorageInterface'))) {
                return null;
            }

            return NodeVisitor::REMOVE_NODE;
        }

        assert($node instanceof Node\Expr\MethodCall);

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\node\NodeStorageInterface'))) {
            return null;
        }

        $methodName = $this->getName($node->name);

        if ($methodName === 'revisionIds') {
            if (count($node->args) !== 1) {
                return null;
            }

            $nodeArg = $node->args[0] instanceof Node\Arg ? $node->args[0]->value : $node->args[0];
            $getQuery = $this->nodeFactory->createMethodCall($node->var, 'getQuery');
            $allRevisions = $this->nodeFactory->createMethodCall($getQuery, 'allRevisions');
            $nodeId = $this->nodeFactory->createMethodCall($nodeArg, 'id');
            $condition = new Node\Expr\MethodCall($allRevisions, 'condition', [
                new Node\Arg(new Node\Scalar\String_('nid')),
                new Node\Arg($nodeId),
            ]);
            $accessCheck = new Node\Expr\MethodCall($condition, 'accessCheck', [
                new Node\Arg(new Node\Expr\ConstFetch(new Node\Name('FALSE'))),
            ]);
            $execute = $this->nodeFactory->createMethodCall($accessCheck, 'execute');

            return $this->nodeFactory->createFuncCall('array_keys', [new Node\Arg($execute)]);
        }

        if ($methodName === 'userRevisionIds') {
            if (count($node->args) !== 1) {
                return null;
            }

            $accountArg = $node->args[0] instanceof Node\Arg ? $node->args[0]->value : $node->args[0];
            $getQuery = $this->nodeFactory->createMethodCall($node->var, 'getQuery');
            $allRevisions = $this->nodeFactory->createMethodCall($getQuery, 'allRevisions');
            $accessCheck = new Node\Expr\MethodCall($allRevisions, 'accessCheck', [
                new Node\Arg(new Node\Expr\ConstFetch(new Node\Name('FALSE'))),
            ]);
            $accountId = $this->nodeFactory->createMethodCall($accountArg, 'id');
            $condition = new Node\Expr\MethodCall($accessCheck, 'condition', [
                new Node\Arg(new Node\Scalar\String_('uid')),
                new Node\Arg($accountId),
            ]);
            $execute = $this->nodeFactory->createMethodCall($condition, 'execute');

            return $this->nodeFactory->createFuncCall('array_keys', [new Node\Arg($execute)]);
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replaces deprecated NodeStorage::revisionIds() and userRevisionIds() with entity queries; removes countDefaultLanguageRevisions() (no replacement)', [
            new CodeSample(
                '$storage->revisionIds($node);',
                "array_keys(\$storage->getQuery()->allRevisions()->condition('nid', \$node->id())->accessCheck(FALSE)->execute());"
            ),
            new CodeSample(
                '$storage->userRevisionIds($account);',
                "array_keys(\$storage->getQuery()->allRevisions()->accessCheck(FALSE)->condition('uid', \$account->id())->execute());"
            ),
            new CodeSample(
                '$storage->countDefaultLanguageRevisions($node);',
                ''
            ),
        ]);
    }
}
