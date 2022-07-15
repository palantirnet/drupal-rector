<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class FileBuildUriRector extends AbstractRector
{
    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_build_uri() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$uri1 = file_build_uri('path/to/file.txt');
$path = 'path/to/other/file.png';
$uri2 = file_build_uri($path);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$uri1 = \Drupal::service('stream_wrapper_manager')->normalizeUri(\Drupal::config('system.file')->get('default_scheme') . ('://' . 'path/to/file.txt'));
$path = 'path/to/other/file.png';
$uri2 = \Drupal::service('stream_wrapper_manager')->normalizeUri(\Drupal::config('system.file')->get('default_scheme') . ('://' . $path));
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
            Node\Expr\FuncCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);
        if ($this->getName($node->name) !== 'file_build_uri') {
            return null;
        }

        assert(count($node->getArgs()) === 1);

        $config = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            'config',
            [new Node\Arg(new Node\Scalar\String_('system.file'))]
        );
        $scheme = new Node\Expr\MethodCall($config, new Node\Identifier('get'), [new Node\Arg(new Node\Scalar\String_('default_scheme'))]);

        $arg = New Node\Arg(new Node\Expr\BinaryOp\Concat(
            $scheme,
            // The nested concatenation is enclosed in parentheses.
            // @see https://github.com/rectorphp/rector/issues/7188
            new Node\Expr\BinaryOp\Concat(
                new Node\Scalar\String_('://'),
                $node->getArgs()[0]->value
            )
        ));

        $service = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            'service',
            [new Node\Arg(new Node\Scalar\String_('stream_wrapper_manager'))]
        );
        $methodName = new Node\Identifier('normalizeUri');

        return new Node\Expr\MethodCall($service, $methodName, [$arg]);
    }
}
