<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated entity-type integer-ID helper methods with EntityTypeInterface::hasIntegerId().
 *
 * Deprecated in drupal:11.4.0, removed in drupal:13.0.0.
 *
 * Handles three patterns, all on $this with one $entityType argument:
 * - $this->getEntityTypeIdKeyType($entityType) === 'integer' => $entityType->hasIntegerId()
 * - $this->entityTypeSupportsComments($entityType)           => $entityType->hasIntegerId()
 * - $this->hasIntegerId($entityType)                         => $entityType->hasIntegerId()
 *
 * @see https://www.drupal.org/node/3566801
 */
final class UseEntityTypeHasIntegerIdRector extends AbstractRector
{
    private const METHOD_OWNER_CLASS = [
        'entityTypeSupportsComments' => 'Drupal\comment\CommentTypeForm',
        'hasIntegerId' => 'Drupal\layout_builder\Plugin\SectionStorage\OverridesSectionStorage',
    ];

    private const GET_ENTITY_TYPE_ID_KEY_TYPE_CLASS = 'Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider';

    public function getNodeTypes(): array
    {
        return [Node\Expr\BinaryOp\Identical::class, Node\Expr\MethodCall::class];
    }

    public function refactor(Node $node): mixed
    {
        if ($node instanceof Node\Expr\BinaryOp\Identical) {
            return $this->refactorIdentical($node);
        }

        assert($node instanceof Node\Expr\MethodCall);

        return $this->refactorMethodCall($node);
    }

    private function refactorIdentical(Node\Expr\BinaryOp\Identical $node): ?Node
    {
        [$methodCall, $string] = $this->extractPair($node);

        if ($methodCall === null || $string === null) {
            return null;
        }

        if (!$this->isThisCall($methodCall, 'getEntityTypeIdKeyType')) {
            return null;
        }

        if (!$this->isObjectType($methodCall->var, new ObjectType(self::GET_ENTITY_TYPE_ID_KEY_TYPE_CLASS))) {
            return null;
        }

        if ($string->value !== 'integer' || count($methodCall->args) !== 1) {
            return null;
        }

        return new Node\Expr\MethodCall($methodCall->args[0]->value, new Node\Identifier('hasIntegerId'));
    }

    private function refactorMethodCall(Node\Expr\MethodCall $node): ?Node
    {
        if (!$node->var instanceof Node\Expr\Variable || $node->var->name !== 'this') {
            return null;
        }

        $name = $this->getName($node->name);
        if ($name === null || !isset(self::METHOD_OWNER_CLASS[$name])) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType(self::METHOD_OWNER_CLASS[$name]))) {
            return null;
        }

        if (count($node->args) !== 1) {
            return null;
        }

        return new Node\Expr\MethodCall($node->args[0]->value, new Node\Identifier('hasIntegerId'));
    }

    /**
     * @return array{Node\Expr\MethodCall|null, Node\Scalar\String_|null}
     */
    private function extractPair(Node\Expr\BinaryOp\Identical $node): array
    {
        if ($node->left instanceof Node\Expr\MethodCall && $node->right instanceof Node\Scalar\String_) {
            return [$node->left, $node->right];
        }
        if ($node->right instanceof Node\Expr\MethodCall && $node->left instanceof Node\Scalar\String_) {
            return [$node->right, $node->left];
        }

        return [null, null];
    }

    private function isThisCall(Node\Expr\MethodCall $node, string $methodName): bool
    {
        if (!$node->var instanceof Node\Expr\Variable || $node->var->name !== 'this') {
            return false;
        }

        return $this->isName($node->name, $methodName);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated entity-type integer-ID helper methods with EntityTypeInterface::hasIntegerId() (drupal:11.4.0)', [
            new CodeSample(
                "\$this->getEntityTypeIdKeyType(\$entity_type) === 'integer'",
                '$entity_type->hasIntegerId()'
            ),
        ]);
    }
}
