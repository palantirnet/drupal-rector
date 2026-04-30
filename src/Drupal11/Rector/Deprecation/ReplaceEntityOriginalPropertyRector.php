<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated $entity->original magic property with getOriginal()/setOriginal().
 *
 * Deprecated in drupal:11.2.0, removed in drupal:12.0.0.
 * Skips $this->original to avoid false positives on non-entity classes.
 *
 * @see https://www.drupal.org/node/3571065
 */
final class ReplaceEntityOriginalPropertyRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [PropertyFetch::class, Assign::class];
    }

    public function refactor(Node $node): mixed
    {
        // Step 1: $entity->original → $entity->getOriginal()
        // (skip $this->original — non-entity classes have a legitimate $original property)
        if ($node instanceof PropertyFetch) {
            if ($this->isName($node->name, 'original') && !$this->isThisVar($node->var)) {
                return new MethodCall($node->var, 'getOriginal');
            }
            return null;
        }

        assert($node instanceof Assign);

        // Step 2: after step 1 transforms the LHS, detect $entity->getOriginal() = $x
        // (invalid assignment target) and rewrite to $entity->setOriginal($x).
        if ($node->var instanceof MethodCall
            && $this->isName($node->var->name, 'getOriginal')
            && empty($node->var->args)
        ) {
            return new MethodCall(
                $node->var->var,
                'setOriginal',
                [new Arg($node->expr)]
            );
        }

        return null;
    }

    private function isThisVar(Node $node): bool
    {
        return $node instanceof Variable && $node->name === 'this';
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated $entity->original magic property with getOriginal()/setOriginal() method calls (drupal:11.2.0)', [
            new CodeSample(
                '$original = $entity->original;',
                '$original = $entity->getOriginal();'
            ),
            new CodeSample(
                '$entity->original = $unchanged;',
                '$entity->setOriginal($unchanged);'
            ),
        ]);
    }
}
