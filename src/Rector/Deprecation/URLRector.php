<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated \Drupal::url() calls.
 *
 * There is no change record for this.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class URLRector extends AbstractRector
{

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated \Drupal::url() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
\Drupal::url('user.login');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('url_generator')->generateFromRoute('user.login');
CODE_AFTER
            )
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\StaticCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\StaticCall $node */
        if ($this->getName($node) === 'url' && $this->getName($node->class) === 'Drupal') {
            $service_name = new Node\Arg(new Node\Scalar\String_('url_generator'));

            $service = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'service', [$service_name]);

            $method_name = new Node\Identifier('generateFromRoute');

            $method_arguments = $node->args;

            $node = new Node\Expr\MethodCall($service, $method_name, $method_arguments);
        }

        return $node;
    }
}
