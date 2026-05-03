<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated $_SESSION['key'] = $value writes with \Drupal::request()->getSession()->set().
 *
 * Deprecated in drupal:11.2.0.
 *
 * @see https://www.drupal.org/node/3518527
 */
final class ReplaceSessionWritesWithRequestSessionRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Assign::class];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Assign);

        if (!$node->var instanceof ArrayDimFetch) {
            return null;
        }

        $arrayDimFetch = $node->var;

        if (!$arrayDimFetch->var instanceof Variable) {
            return null;
        }

        if ($this->getName($arrayDimFetch->var) !== '_SESSION') {
            return null;
        }

        if ($arrayDimFetch->dim === null) {
            return null;
        }

        $drupalRequest = new StaticCall(
            new FullyQualified('Drupal'),
            'request',
            []
        );

        $getSession = new MethodCall($drupalRequest, 'getSession', []);

        return new MethodCall(
            $getSession,
            'set',
            [
                new Arg($arrayDimFetch->dim),
                new Arg($node->expr),
            ]
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated $_SESSION writes with \\Drupal::request()->getSession()->set() (drupal:11.2.0)', [
            new CodeSample(
                '$_SESSION[\'my_key\'] = $value;',
                '\\Drupal::request()->getSession()->set(\'my_key\', $value);'
            ),
        ]);
    }
}
