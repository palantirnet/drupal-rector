<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use DrupalRector\Services\AddCommentService;
use DrupalRector\Utility\FindParentByTypeTrait;
use PhpParser\Node;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated \Drupal\Core\Routing\LinkGeneratorTrait::l() calls.
 *
 * See https://www.drupal.org/node/2614344 for change record.
 *
 * What is covered:
 * - Trait usage when the `LinkGeneratorTrait` is already present on the class
 *
 * Improvement opportunities
 * - Remove link generator trait.
 */
final class LinkGeneratorTraitLRector extends AbstractRector
{
    use FindParentByTypeTrait;

    /**
     * @var AddCommentService
     */
    private AddCommentService $commentService;

    public function __construct(AddCommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated l() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->l($text, $url);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal\Core\Link::fromTextAndUrl($text, $url);
CODE_AFTER
            ),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Stmt\Expression::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Stmt\Expression);

        if (!($node->expr instanceof Node\Expr\MethodCall)) {
            return null;
        }

        $expr = $node->expr;

        /** @var Node\Expr\MethodCall $expr */
        if ($this->getName($expr->name) === 'l') {
            $scope = $node->getAttribute(AttributeKey::SCOPE);
            $classReflection = $scope->getClassReflection();
            if (!is_null($classReflection) && $classReflection->hasTraitUse('Drupal\Core\Routing\LinkGeneratorTrait')) {
                $this->commentService->addDrupalRectorComment($node, 'Please manually remove the `use LinkGeneratorTrait;` statement from this class.');

                // Replace with a static call to Link::fromTextAndUrl().
                $name = new Node\Name\FullyQualified('Drupal\Core\Link');
                $call = new Node\Identifier('fromTextAndUrl');

                $node->expr = new Node\Expr\StaticCall($name, $call, $expr->args);

                return $node;
            }
        }

        return null;
    }
}
