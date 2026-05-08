<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated twig_extension() with the '.html.twig' string literal.
 *
 * @see https://www.drupal.org/node/1685492
 */
class ReplaceTwigExtensionRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            "Replace deprecated twig_extension() with the '.html.twig' string literal",
            [
                new CodeSample(
                    '$ext = twig_extension();',
                    "\$ext = '.html.twig';"
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /** @param FuncCall $node */
    public function refactor(Node $node): ?Node
    {
        if ($this->getName($node) === 'twig_extension') {
            return new String_('.html.twig');
        }

        return null;
    }
}
