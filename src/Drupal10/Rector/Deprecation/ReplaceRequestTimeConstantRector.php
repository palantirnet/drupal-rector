<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the deprecated REQUEST_TIME constant with \Drupal::time()->getRequestTime().
 *
 * Deprecated in drupal:8.3.0, removed in drupal:11.0.0.
 *
 * @see https://www.drupal.org/node/3395986
 */
final class ReplaceRequestTimeConstantRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Node\Expr\ConstFetch::class];
    }

    public function refactor(Node $node): mixed
    {
        assert($node instanceof Node\Expr\ConstFetch);

        if (!$this->isName($node, 'REQUEST_TIME')) {
            return null;
        }

        $staticCall = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            new Node\Identifier('time')
        );

        return new Node\Expr\MethodCall($staticCall, new Node\Identifier('getRequestTime'));
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated REQUEST_TIME constant with \\Drupal::time()->getRequestTime() (deprecated drupal:8.3.0, removed drupal:11.0.0)', [
            new CodeSample(
                '$cutoff = REQUEST_TIME - $lifespan;',
                '$cutoff = \\Drupal::time()->getRequestTime() - $lifespan;'
            ),
        ]);
    }
}
