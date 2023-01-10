<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class UserPasswordRector extends AbstractRector
{
    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated user_password() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$pass = user_password();
$shorter_pass = user_password(8);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$pass = \Drupal::service('password_generator')->generate();
$shorter_pass = \Drupal::service('password_generator')->generate(8);
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
        if ($this->getName($node->name) !== 'user_password') {
            return null;
        }

        $service = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            'service',
            [new Node\Arg(new Node\Scalar\String_('password_generator'))]
        );
        $methodName = new Node\Identifier('generate');
        return new Node\Expr\MethodCall($service, $methodName, $node->getArgs());
    }


}
