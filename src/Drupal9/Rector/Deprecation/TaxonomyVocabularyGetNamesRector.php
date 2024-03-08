<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class TaxonomyVocabularyGetNamesRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Refactor function call to an entity storage method',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
        $vids = taxonomy_vocabulary_get_names();
        CODE_BEFORE
                    ,
                    <<<'CODE_AFTER'
        $vids = \Drupal::entityQuery('taxonomy_vocabulary')->execute();
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

        if ($this->getName($node->name) !== 'taxonomy_vocabulary_get_names') {
            return null;
        }

        $entityQuery = $this->nodeFactory->createStaticCall('Drupal', 'entityQuery', $this->nodeFactory->createArgs(['taxonomy_vocabulary']));

        return $this->nodeFactory->createMethodCall($entityQuery, 'execute');
    }
}
