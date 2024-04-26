<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Drupal10\Rector\ValueObject\VersionedFunctionToServiceConfiguration;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated function call with service method call with backwards compatibility.
 */
class VersionedFunctionToServiceRector extends AbstractDrupalCoreRector
{
    /**
     * @var array|VersionedFunctionToServiceConfiguration[]
     */
    protected array $configurations = [];

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof VersionedFunctionToServiceConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', VersionedFunctionToServiceConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    /**
     * @param Node\Expr\FuncCall                      $node
     * @param VersionedFunctionToServiceConfiguration $configuration
     *
     * @return Node|null
     */
    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        /** @var Node\Expr\FuncCall $node */
        if ($this->getName($node->name) === $configuration->getDeprecatedFunctionName()) {
            // This creates a service call like `\Drupal::service('file_system').
            $service = new Node\Expr\StaticCall(new Node\Name\FullyQualified('Drupal'), 'service', [new Node\Arg(new Node\Scalar\String_($configuration->getServiceName()))]);

            $method_name = new Node\Identifier($configuration->getServiceMethodName());

            return new Node\Expr\MethodCall($service, $method_name, $node->args);
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated function to service calls, used in Drupal 8 and 9 deprecations', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
_drupal_flush_css_js();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('asset.query_string')->reset();
CODE_AFTER
                ,
                [
                    new VersionedFunctionToServiceConfiguration('10.2.0', '_drupal_flush_css_js', 'asset.query_string', 'reset'),
                ]
            ),
        ]);
    }
}
