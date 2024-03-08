<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class FileCreateUrlRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_create_url() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
file_create_url($uri);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('file_url_generator')->generateAbsoluteString($uri);
CODE_AFTER
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);
        if ($this->getName($node->name) !== 'file_create_url') {
            return null;
        }
        if ($node->hasAttribute('parent')) {
            $parent = $node->getAttribute('parent');
            if ($parent instanceof Node\Arg) {
                return null;
            }
        }

        $service = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            'service',
            [new Node\Arg(new Node\Scalar\String_('file_url_generator'))]
        );
        $methodName = new Node\Identifier('generateAbsoluteString');

        return new Node\Expr\MethodCall($service, $methodName, $node->getArgs());
    }
}
