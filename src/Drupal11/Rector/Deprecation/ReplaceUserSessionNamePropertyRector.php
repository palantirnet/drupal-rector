<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated $userSession->name property read with $userSession->getAccountName().
 *
 * Deprecated in drupal:11.3.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3513856
 */
final class ReplaceUserSessionNamePropertyRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Expr\PropertyFetch::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\PropertyFetch);

        if (!$this->isName($node->name, 'name')) {
            return null;
        }

        // Skip $this->name: protected property access within UserSession is
        // not deprecated and must not be rewritten to avoid infinite recursion
        // (UserSession::getAccountName() itself reads $this->name).
        if ($node->var instanceof Variable && $this->getName($node->var) === 'this') {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Session\UserSession'))) {
            return null;
        }

        return new Node\Expr\MethodCall($node->var, new Node\Identifier('getAccountName'));
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated $userSession->name property read with $userSession->getAccountName() (drupal:11.3.0)', [
            new CodeSample(
                '$name = $userSession->name;',
                '$name = $userSession->getAccountName();'
            ),
        ]);
    }
}
