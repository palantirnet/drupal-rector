<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\AddCommentTrait;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;

/**
 * Replaces deprecated function call to EntityInterface::link.
 *
 * See https://www.drupal.org/node/2614344 for change record.
 *
 * What is covered:
 * - Changes the name of the method and adds toString().
 *
 * Improvement opportunities:
 * - Checks the variable has a certain class.
 */
final class EntityInterfaceLinkRector extends AbstractRector implements ConfigurableRectorInterface
{

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
        return new RuleDefinition('Fixes deprecated link() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$url = $entity->link();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$url = $entity->toLink()->toString();
CODE_AFTER
            ),
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
        // TODO: Check the class to see if it implements Drupal\Core\Entity\EntityInterface.
        if ($this->getName($node->name) === 'link') {
            $node_class_name = $this->getName($node->var);

            $this->addDrupalRectorComment($node,
                "Please confirm that `$$node_class_name` is an instance of `\Drupal\Core\Entity\EntityInterface`. Only the method name and not the class name was checked for this replacement, so this may be a false positive.");

            $toLink_node = $node;

            $toLink_node->name = new Node\Identifier('toLink');

            // Add ->toString();
            $new_node = new Node\Expr\MethodCall($toLink_node,
                new Node\Identifier('toString'));

            return $new_node;
        }

        return null;
    }

}
