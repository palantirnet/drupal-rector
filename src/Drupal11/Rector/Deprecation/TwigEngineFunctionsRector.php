<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated twig_extension() and twig_render_template() with TwigThemeEngine equivalents.
 *
 * @see https://www.drupal.org/node/1685492
 */
class TwigEngineFunctionsRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated twig_extension() and twig_render_template() calls with TwigThemeEngine service equivalents',
            [
                new CodeSample(
                    '$ext = twig_extension();',
                    "\$ext = '.html.twig';"
                ),
                new CodeSample(
                    '$output = twig_render_template($template_file, $variables);',
                    '$output = \\Drupal::service(\\Drupal\\Core\\Template\\TwigThemeEngine::class)->renderTemplate($template_file, $variables);'
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
        $name = $this->getName($node);

        if ($name === 'twig_extension') {
            return new String_('.html.twig');
        }

        if ($name === 'twig_render_template') {
            return new MethodCall(
                new StaticCall(
                    new FullyQualified('Drupal'),
                    'service',
                    [new Node\Arg(
                        new ClassConstFetch(
                            new FullyQualified('Drupal\Core\Template\TwigThemeEngine'),
                            'class'
                        )
                    )]
                ),
                'renderTemplate',
                $node->args
            );
        }

        return null;
    }
}
