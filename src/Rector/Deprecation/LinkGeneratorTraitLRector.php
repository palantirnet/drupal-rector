<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\AddCommentTrait;
use DrupalRector\Utility\TraitsByClassHelperTrait;
use PhpParser\Node;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Core\Rector\AbstractRector;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
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
final class LinkGeneratorTraitLRector extends AbstractRector implements ConfigurableRectorInterface
{
    use TraitsByClassHelperTrait;
    use AddCommentTrait;

    public function configure(array $configuration): void
    {
        $this->configureNoticesAsComments($configuration);
    }

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated l() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
$this->l($text, $url);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal\Core\Link::fromTextAndUrl($text, $url);
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
            Node\Expr\MethodCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /** @var Node\Expr\MethodCall $node */
          if ($this->getName($node->name) === 'l') {
              $class = $this->betterNodeFinder->findParentType($node, Node\Stmt\Class_::class);

            // Check if class has LinkGeneratorTrait.
            if ($this->checkClassTypeHasTrait($class, 'Drupal\Core\Routing\LinkGeneratorTrait')) {
              $this->addDrupalRectorComment($node, 'Please manually remove the `use LinkGeneratorTrait;` statement from this class.');

              // Replace with a static call to Link::fromTextAndUrl().
              $name = new Node\Name\FullyQualified('Drupal\Core\Link');
              $call = new Node\Identifier('fromTextAndUrl');

              $node = new Node\Expr\StaticCall($name, $call, $node->args);

              return $node;
            }
        }

        return null;
    }
}
