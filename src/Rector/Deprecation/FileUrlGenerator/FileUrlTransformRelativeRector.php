<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation\FileUrlGenerator;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class FileUrlTransformRelativeRector extends AbstractRector
{

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_url_transform_relative() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
file_url_transform_relative($uri);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('file_url_generator')->transformRelative($uri);
CODE_AFTER
            )
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
        if ($this->getName($node->name) !== 'file_url_transform_relative') {
            return null;
        }
        $methodName = 'transformRelative';

        $args = $node->getArgs();
        if (count($args) === 1) {
            $fileUrlArg = $args[0]->value;
            if ($fileUrlArg instanceof Node\Expr\FuncCall
                && $this->getName($fileUrlArg->name) === 'file_create_url'
            ) {
                $args = $fileUrlArg->getArgs();
                $methodName = 'generateString';
            }
        }

        $service = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            'service',
            [new Node\Arg(new Node\Scalar\String_('file_url_generator'))]
        );
        return new Node\Expr\MethodCall($service, new Node\Identifier($methodName), $args);
    }

}
