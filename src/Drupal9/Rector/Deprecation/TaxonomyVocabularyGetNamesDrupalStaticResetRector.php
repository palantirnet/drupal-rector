<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\Deprecation;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class TaxonomyVocabularyGetNamesDrupalStaticResetRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Refactor drupal_static_reset(\'taxonomy_vocabulary_get_names\') to entity storage reset cache',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
        drupal_static_reset('taxonomy_vocabulary_get_names');
        CODE_BEFORE
                    ,
                    <<<'CODE_AFTER'
        \Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->resetCache();
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

        if ($this->getName($node->name) !== 'drupal_static_reset') {
            return null;
        }

        $args = $node->getArgs();
        if (count($args) !== 1) {
            return null;
        }

        $firstValue = $args[0]->value;
        if (!($firstValue instanceof Node\Scalar\String_) || $firstValue->value !== 'taxonomy_vocabulary_get_names') {
            return null;
        }

        $entityQuery = $this->nodeFactory->createStaticCall('Drupal', 'entityTypeManager');
        $storage = $this->nodeFactory->createMethodCall($entityQuery, 'getStorage', $this->nodeFactory->createArgs(['taxonomy_vocabulary']));

        return $this->nodeFactory->createMethodCall($storage, 'resetCache');
    }
}
