<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\ValueObject\FunctionToEntityTypeStorageConfiguration;
use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class TaxonomyVocabularyGetNamesRector extends AbstractRector {


    public function getRuleDefinition(): RuleDefinition {
        return new RuleDefinition('Refactor function call to an entity storage method',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
        $vids = taxonomy_vocabulary_get_names();
        CODE_BEFORE
                    ,
                    <<<'CODE_AFTER'
        $vids = \Drupal::entityQuery('taxonomy_vocabulary')->execute();
        CODE_AFTER
                    ,
                    [
                        new FunctionToEntityTypeStorageConfiguration('taxonomy_terms_static_reset', 'taxonomy_term', 'resetCache'),
                        new FunctionToEntityTypeStorageConfiguration('taxonomy_vocabulary_static_reset', 'taxonomy_vocabulary', 'resetCache'),
                    ]
                ),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getNodeTypes(): array {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    public function refactor(Node $node): ?Node {
        assert($node instanceof Node\Expr\FuncCall);


        if ($this->getName($node->name) !== 'taxonomy_vocabulary_get_names') {
            return null;
        }

        $entityQuery = $this->nodeFactory->createStaticCall('Drupal', 'entityQuery', $this->nodeFactory->createArgs(['taxonomy_vocabulary']));
        return $this->nodeFactory->createMethodCall($entityQuery, 'execute');

    }

}
