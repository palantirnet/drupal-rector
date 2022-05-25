<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Utility\AddCommentTrait;
use DrupalRector\Utility\TraitsByClassHelperTrait;
use PhpParser\Comment;
use PhpParser\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\Generic\GenericClassStringType;
use PHPStan\Type\StringType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfo;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated drupal_set_message() calls.
 *
 * See https://www.drupal.org/node/2774931 for change record.
 *
 * What is covered:
 * - Static replacement
 * - Trait usage when the `MessengerTrait` is already present on the class
 *
 * Improvement opportunities
 * - Handle variables used to specify the type
 *   - Example, `drupal_set_message('my message', $type)`
 * - Add trait for classes
 *   - `use MessengerTrait;`
 */
final class DrupalSetMessageRector extends AbstractRector implements ConfigurableRectorInterface
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
        return new RuleDefinition('Fixes deprecated drupal_set_message() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
drupal_set_message('example status', 'status');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::messenger()->addStatus('example status');
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
        /** @var Node\Expr\FuncCall $node */
        if ($this->getName($node->name) === 'drupal_set_message') {
            $class = $this->betterNodeFinder->findParentType($node, Node\Stmt\Class_::class);
            if ($this->checkClassTypeHasTrait($class, 'Drupal\Core\Messenger\MessengerTrait')) {
                $messenger_service = new Node\Expr\MethodCall(
                    new Node\Expr\Variable('this'),
                    new Node\Identifier('messenger')
                );
            } else {
                // TODO: Add the messanger trait to a class that doesn't have it.
                // For now, we are using a static call.
                $messenger_service = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'messenger');
            }

            $method_name = 'addStatus';

            $message = $node->args[0];

            $method_arguments = [
                $message,
            ];

            // Message's type parameter is optional. Use it if present.
            if (array_key_exists(1, $node->args)) {
                $messageTypeArgType = $this->nodeTypeResolver->getType($node->args[1]->value);
                $messageType = '';

                if ($messageTypeArgType instanceof ConstantStringType) {
                    $messageType = $messageTypeArgType->getValue();
                } elseif ($node->args[1]->value instanceof Node\Scalar\String_) {
                    $messageType = $node->args[1]->value->value;
                }

                if ($messageType !== '') {
                    if (in_array($messageType, ['error', 'status', 'warning'], true)) {
                        $method_name = 'add' . ucfirst($messageType);
                    } else {
                        $method_name = 'addMessage';
                        $method_arguments[] = $node->args[1];
                    }
                } else {
                    /*
                     * For now, if we hit a more complex situation, we don't process this instance of the depracation.
                     *
                     * TODO: Address more complex situations.
                     *
                     * Unable to identify type, because it coming from a variable that might exist at runtime.
                     * We would need to do a more complex rule like adding a switch statement around the variable to determine what method to call.
                     *
                     * We could add the switch statement or at least add a message to the user to address this.
                     *
                     * The switch statement might be something like,
                     *
                     * switch($type) {
                     *   case 'warning':
                     *     $this->messenger()->addWarning($message);
                     *     break;
                     *   case 'error':
                     *     $this->messenger()->addError($message);
                     *     break;
                     *   default:
                     *     $this->messenger()->addStatus($message);
                     * }
                     * https://git.drupalcode.org/project/devel/blob/8.x-2.0/devel.module#L151
                     * https://git.drupalcode.org/project/devel/blob/8.x-2.0/devel.module#L265
                     */
                    $this->addDrupalRectorComment(
                        $node,
                        'This needs to be replaced, but Rector was not yet able to replace this because the type of message was set with a variable. If you need to continue to use a variable, you might consider using a switch statement.'
                    );
                    // Since we did not rename the function, Rector will process
                    // this node multiple times. So we need to flag it with the
                    // @noRector tag.
                    $parent_node = $node->getAttribute(AttributeKey::PARENT_NODE);
                    assert($parent_node instanceof Node);
                    $comments = $parent_node->getAttribute(AttributeKey::COMMENTS);
                    $comments[] = new Comment('// @noRector');
                    $parent_node->setAttribute(AttributeKey::COMMENTS, $comments);

                    // The comments for this node have already been processed
                    // and stored in an object hash. We need to manually add the
                    // tag ourselves to the phpDoc object to prevent further
                    // processing.
                    $phpDocInfo = $node->getAttribute(AttributeKey::PHP_DOC_INFO);
                    assert($phpDocInfo instanceof PhpDocInfo);
                    $phpDocInfo->addPhpDocTagNode(new PhpDocTagNode('@noRector', new GenericTagValueNode('')));

                    return $node;
                }
            }

            // Add the third argument if present.
            if (array_key_exists(2, $node->args)) {
                $method_arguments[] = $node->args[2];
            }

            $method = new Node\Identifier($method_name);

            $node = new Node\Expr\MethodCall($messenger_service, $method, $method_arguments);

            return $node;
        }

        return null;
    }
}
