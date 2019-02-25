<?php

declare(strict_types=1);

namespace Mxr576\Rector\Property;

use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use Rector\NodeTypeResolver\Node\Attribute;
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
            $classNode = $node->getAttribute(Attribute::CLASS_NODE);
            // Ignore procedural code.
            if ($classNode === NULL) {
                return $node;
            }
            if ($this->isType($classNode, $type)) {
                if (array_key_exists($node->name->name, $propertyToMethod)) {
                    // Ignore non method calls.
                    if (null === $node->getAttribute(Attribute::NEXT_NODE) || !$node->getAttribute(Attribute::NEXT_NODE) instanceof Identifier) {
                        continue;
                    }

                    if ($node->getAttribute(Attribute::METHOD_NAME) === $propertyToMethod[$node->name->name]) {
                        // Sanity check.
                        // Do not replace the property within the method that
                        // should replace the property.
                        continue;
                    }

                    return $this->createMethodCall($node->var, $propertyToMethod[$node->name->name], []);
                }
            }
        }

        return $node;
    }
}
