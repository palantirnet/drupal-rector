<?php

declare(strict_types=1);

namespace Drupal8Rector\Rector\Property;

use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\ConfiguredCodeSample;
use Rector\RectorDefinition\RectorDefinition;

final class PropertyToMethodCallRector extends AbstractRector
{
    /**
     * @var string[][]
     */
    private $propertyToMethod = [];

    /**
     * PropertyToMethodCallRector constructor.
     *
     * @param array $propertyToMethod
     */
    public function __construct(array $propertyToMethod)
    {
        $this->propertyToMethod = $propertyToMethod;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Replaces method calls on a property with a method call on a method.', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
$this->messenger->addStatus($this->t('Foo bar'));
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$this->messenger()->addStatus($this->t('Foo bar'));
CODE_SAMPLE
                ,
                [
                    '$propertyToMethod' => [
                        'Drupal\Core\Messenger\MessengerTrait' => [
                            'messenger' => 'messenger',
                        ],
                    ],
                ]
            ), ]);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [PropertyFetch::class];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        /* @var \PhpParser\Node\Expr\PropertyFetch $node **/
        foreach ($this->propertyToMethod as $type => $propertyToMethod) {
            $classNode = $node->getAttribute(AttributeKey::CLASS_NODE);
            // Ignore procedural code.
            if (null === $classNode) {
                return $node;
            }
            if ($this->isType($classNode, $type)) {
                $propertyName = (string) $node->name->name;
                if (array_key_exists($propertyName, $propertyToMethod)) {
                    // Ignore non method calls.
                    if (null === $node->getAttribute(AttributeKey::NEXT_NODE) || !$node->getAttribute(AttributeKey::NEXT_NODE) instanceof Identifier) {
                        continue;
                    }

                    if ($node->getAttribute(AttributeKey::METHOD_NAME) === $propertyToMethod[$propertyName]) {
                        // Sanity check.
                        // Do not replace the property within the method that
                        // should replace the property.
                        continue;
                    }

                    return $this->createMethodCall($node->var, $propertyToMethod[$propertyName], []);
                }
            }
        }

        return $node;
    }
}
