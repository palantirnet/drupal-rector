<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated DRUPAL_DISABLED/OPTIONAL/REQUIRED constants and integer literals
 * in NodeTypeInterface::setPreviewMode() calls with NodePreviewMode enum cases.
 *
 * Deprecated in drupal:11.3.0, removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3538277
 * @see https://www.drupal.org/node/3538666
 */
final class ReplaceNodeSetPreviewModeRector extends AbstractRector
{
    private const CONST_TO_ENUM = [
        'DRUPAL_DISABLED' => 'Disabled',
        'DRUPAL_OPTIONAL' => 'Optional',
        'DRUPAL_REQUIRED' => 'Required',
    ];

    private const INT_TO_ENUM = [
        0 => 'Disabled',
        1 => 'Optional',
        2 => 'Required',
    ];

    public function getNodeTypes(): array
    {
        return [Node\Expr\MethodCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\MethodCall);

        if (!$this->isName($node->name, 'setPreviewMode')) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\node\NodeTypeInterface'))) {
            return null;
        }

        if (count($node->args) !== 1) {
            return null;
        }

        $argValue = $node->args[0]->value;
        $enumCase = null;

        if ($argValue instanceof Node\Expr\ConstFetch) {
            $constName = $this->getName($argValue);
            if ($constName !== null && isset(self::CONST_TO_ENUM[$constName])) {
                $enumCase = self::CONST_TO_ENUM[$constName];
            }
        }

        if ($enumCase === null && $argValue instanceof Node\Scalar\LNumber) {
            $enumCase = self::INT_TO_ENUM[$argValue->value] ?? null;
        }

        if ($enumCase === null) {
            return null;
        }

        $node->args[0] = new Node\Arg(
            new Node\Expr\ClassConstFetch(
                new Node\Name\FullyQualified('Drupal\node\NodePreviewMode'),
                $enumCase
            )
        );

        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated DRUPAL_DISABLED/OPTIONAL/REQUIRED constants and integer literals in setPreviewMode() with NodePreviewMode enum cases (drupal:11.3.0)', [
            new CodeSample(
                '$nodeType->setPreviewMode(DRUPAL_DISABLED);',
                '$nodeType->setPreviewMode(\\Drupal\\node\\NodePreviewMode::Disabled);'
            ),
        ]);
    }
}
