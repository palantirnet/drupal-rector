<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation\FileUrlGenerator;

use DrupalRector\Utility\GetDeclaringSourceTrait;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class FromUriRector extends AbstractRector
{

    use GetDeclaringSourceTrait;

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated file_create_url() calls from \Drupal\Core\Url::fromUri().', [
            new CodeSample(
                <<<'CODE_BEFORE'
\Drupal\Core\Url::fromUri(file_create_url($uri));
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('file_url_generator')->generate($uri);
CODE_AFTER
            )
        ]);
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Expr\StaticCall::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\StaticCall);
        if ($this->getName($node->name) !== 'fromUri') {
            return null;
        }
        $type = $this->nodeTypeResolver->getType($node);
        if (!$type instanceof FullyQualifiedObjectType
            || $type->getClassName() !== 'Drupal\Core\Url') {
            return null;
        }
        $args = $node->getArgs();
        if (count($args) !== 1) {
            return null;
        }
        $fileUrlArg = $args[0]->value;
        if (!$fileUrlArg instanceof Node\Expr\FuncCall
            || $this->getName($fileUrlArg->name) !== 'file_create_url'
        ) {
            return null;
        }
        $args = $fileUrlArg->getArgs();
        $service = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            'service',
            [new Node\Arg(new Node\Scalar\String_('file_url_generator'))]
        );
        return new Node\Expr\MethodCall($service, new Node\Identifier('generate'), $args);
    }

}
