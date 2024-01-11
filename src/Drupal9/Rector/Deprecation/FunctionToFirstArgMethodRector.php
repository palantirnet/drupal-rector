<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\Deprecation;

use DrupalRector\Drupal9\Rector\ValueObject\FunctionToFirstArgMethodConfiguration;
use PhpParser\Node;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class FunctionToFirstArgMethodRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var FunctionToFirstArgMethodConfiguration[]
     */
    private array $configuration;

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof FunctionToFirstArgMethodConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', FunctionToFirstArgMethodConfiguration::class));
            }
        }

        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\FuncCall);

        foreach ($this->configuration as $configuration) {
            if ($this->getName($node) !== $configuration->getDeprecatedFunctionName()) {
                continue;
            }
            $args = $node->getArgs();
            if (count($args) !== 1) {
                continue;
            }

            return $this->nodeFactory->createMethodCall($args[0]->value, $configuration->getMethodName());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated taxonomy_implode_tags() calls', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$url = taxonomy_term_uri($term);
$name = taxonomy_term_title($term);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$url = $term->toUrl();
$name = $term->label();
CODE_AFTER
                ,
                [
                    new FunctionToFirstArgMethodConfiguration('taxonomy_term_uri', 'toUrl'),
                    new FunctionToFirstArgMethodConfiguration('taxonomy_term_title', 'label'),
                ]
            ),
        ]);
    }
}
