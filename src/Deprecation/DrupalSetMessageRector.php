<?php

namespace Mxr576\Rector\Deprecation;

use Mxr576\Rector\Utility\TraitsByClassHelperTrait;
use PhpParser\Node;
use Rector\NodeTypeResolver\Node\Attribute;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated drupal_set_message() calls.
 */
final class DrupalSetMessageRector extends AbstractRector
{
    use TraitsByClassHelperTrait;

    private const MESSENGER_TRAIT = 'Drupal\Core\Messenger\MessengerTrait';

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
        // Ignore those complex cases when function name specified by a variable.
        if ($node->name instanceof Node\Name\FullyQualified && 'drupal_set_message' === (string) $node->name) {
            $className = $node->getAttribute(Attribute::CLASS_NAME);
            // If drupal_set_message() called in a class and class uses the MessengerTrait trait then do not replace
            // the function call with a method call on the static \Drupal:messenger() method.
            if ($className && in_array(self::MESSENGER_TRAIT, $this->getTraitsByClass($className))) {
                $baseMethod = new Node\Expr\MethodCall(new Node\Expr\Variable('this'), new Node\Identifier('messenger'));
            } else {
                $baseMethod = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'messenger');
            }

            $methodArgs = [$node->args[0]];

            // Message's type parameter is optional.
            if (array_key_exists(1, $node->args)) {
                if (method_exists($node->args[1]->value, '__toString')) {
                    $methodNameSuffixByMessageType = (string) $node->args[1]->value;
                } elseif ($node->args[1]->value instanceof Node\Scalar\String_) {
                    $methodNameSuffixByMessageType = $node->args[1]->value->value;
                } else {
                    // Unable to identify type, because it coming from a variable or such.
                    // https://git.drupalcode.org/project/devel/blob/8.x-2.0/devel.module#L151
                    // https://git.drupalcode.org/project/devel/blob/8.x-2.0/devel.module#L265
                    $methodNameSuffixByMessageType = 'message';
                    $methodArgs[] = $node->args[1]->value;
                }
            } else {
                $methodNameSuffixByMessageType = 'status';
            }

            // $clear_queue ($repeat) parameter is optional.
            if (array_key_exists(2, $node->args)) {
                $methodArgs[] = $node->args[2];
            }

            $node = new Node\Expr\MethodCall($baseMethod, new Node\Identifier('add' . ucfirst($methodNameSuffixByMessageType)), $methodArgs);
        }

        return $node;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition(sprintf('Fixes deprecated drupal_set_message() calls'));
    }
}
