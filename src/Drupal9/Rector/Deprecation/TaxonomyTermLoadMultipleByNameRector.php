<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class TaxonomyTermLoadMultipleByNameRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Refactor function call to an entity storage method',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
        $terms = taxonomy_term_load_multiple_by_name(
            'Foo',
            'topics'
        );
        CODE_BEFORE
                    ,
                    <<<'CODE_AFTER'
        $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
            'name' => 'Foo',
            'vid' => 'topics',
        ]);
        CODE_AFTER
                ),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);

        if ($this->getName($node->name) !== 'taxonomy_term_load_multiple_by_name') {
            return null;
        }

        $entityQuery = $this->nodeFactory->createStaticCall('Drupal', 'entityTypeManager');
        $storage = $this->nodeFactory->createMethodCall($entityQuery, 'getStorage', $this->nodeFactory->createArgs(['taxonomy_term']));
        $arguments = $this->nodeFactory->createArgs([$this->nodeFactory->createArray([
            'name' => $node->getArgs()[0],
            'vid' => $node->getArgs()[1],
        ])]);

        return $this->nodeFactory->createMethodCall($storage, 'loadByProperties', $arguments);
    }
}
