<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes deprecated trustData() method calls from config entity chains.
 *
 * trustData() is deprecated in drupal:11.4.0 and removed in drupal:13.0.0.
 * It was a no-op optimisation hint; removing it is safe.
 *
 * @see https://www.drupal.org/node/3347842
 */
final class RemoveTrustDataCallRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated trustData() calls from config entity method chains',
            [
                new CodeSample(
                    '$entity->trustData()->save();',
                    '$entity->save();'
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
        assert($node instanceof MethodCall);
        if (!$this->isName($node->name, 'trustData')) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Config\Entity\ConfigEntityInterface'))) {
            return null;
        }

        return $node->var;
    }
}
