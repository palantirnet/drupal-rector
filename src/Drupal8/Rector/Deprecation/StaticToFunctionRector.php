<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use DrupalRector\Drupal8\Rector\ValueObject\StaticToFunctionConfiguration;
use PhpParser\Node;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated static call with a function call.
 *
 * What is covered:
 * - Static replacement
 */
class StaticToFunctionRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var StaticToFunctionConfiguration[]
     */
    private array $staticToFunctionConfigurations;

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\StaticCall::class,
        ];
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof StaticToFunctionConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', StaticToFunctionConfiguration::class));
            }
        }

        $this->staticToFunctionConfigurations = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function refactor(Node $node): ?Node
    {
        foreach ($this->staticToFunctionConfigurations as $configuration) {
            /** @var Node\Expr\StaticCall $node */
            if ($this->getName($node->name) === $configuration->getDeprecatedMethodName() && $this->getName($node->class) === $configuration->getDeprecatedFullyQualifiedClassName()) {
                $method_name = new Node\Name($configuration->getFunctionName());

                return new Node\Expr\FuncCall($method_name, $node->args);
            }
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal\Component\Utility\Unicode::strlen() calls', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$length = \Drupal\Component\Utility\Unicode::strlen('example');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$length = mb_strlen('example');
CODE_AFTER
                ,
                [
                    new StaticToFunctionConfiguration('Drupal\Component\Utility\Unicode', 'strlen', 'mb_strlen'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$string = \Drupal\Component\Utility\Unicode::strtolower('example');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$string = mb_strtolower('example');
CODE_AFTER
                ,
                [
                    new StaticToFunctionConfiguration('Drupal\Component\Utility\Unicode', 'strtolower', 'mb_strtolower'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$string = \Drupal\Component\Utility\Unicode::substr('example', 0, 2);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$string = mb_substr('example', 0, 2);
CODE_AFTER
                ,
                [
                    new StaticToFunctionConfiguration('Drupal\Component\Utility\Unicode', 'substr', 'mb_substr'),
                ]
            ),
        ]);
    }
}
