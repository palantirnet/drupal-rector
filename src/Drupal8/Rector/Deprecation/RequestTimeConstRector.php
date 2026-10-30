<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Rector\VersionBonding\Contract\ComposerPackageConstraintInterface;
use Rector\VersionBonding\ValueObject\ComposerPackageConstraint;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RequestTimeConstRector extends AbstractRector implements ComposerPackageConstraintInterface, DocumentedRuleInterface
{
    public function provideComposerPackageConstraint(): ComposerPackageConstraint
    {
        return new ComposerPackageConstraint('drupal/core', '>=8.3.0');
    }

    protected string $deprecatedConstant = 'REQUEST_TIME';

    /**
     * {@inheritDoc}
     */
    public function getNodeTypes(): array
    {
        return [Node\Expr\ConstFetch::class];
    }

    /**
     * {@inheritDoc}
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\ConstFetch);

        if (!$this->isName($node->name, $this->deprecatedConstant)) {
            return null;
        }

        $service = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'time');
        $method_name = new Node\Identifier('getRequestTime');

        $node = new Node\Expr\MethodCall($service, $method_name);

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated REQUEST_TIME calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$request_time = REQUEST_TIME;
CODE_BEFORE,
                <<<'CODE_AFTER'
$request_time = \Drupal::time()->getRequestTime();
CODE_AFTER
            ),
        ]);
    }
}
