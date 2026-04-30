<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class LanguageModuleFunctionDeprecationsRector extends AbstractDrupalCoreRector
{
    /**
     * @var DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)
    {
        if (!$node instanceof FuncCall) {
            return null;
        }

        if ($this->isName($node, 'language_configuration_element_submit')) {
            return $this->nodeFactory->createStaticCall(
                'Drupal\language\Element\LanguageConfiguration',
                'submit',
                $node->args
            );
        }

        if ($this->isName($node, 'language_process_language_select')) {
            $serviceCall = $this->nodeFactory->createStaticCall('Drupal', 'service', [
                $this->nodeFactory->createClassConstReference('Drupal\language\Hook\LanguageHooks'),
            ]);

            return $this->nodeFactory->createMethodCall($serviceCall, 'processLanguageSelect', $node->args);
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated language module procedural functions with their OOP replacements',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
language_configuration_element_submit($form, $form_state);
CODE_BEFORE,
                    <<<'CODE_AFTER'
\Drupal\language\Element\LanguageConfiguration::submit($form, $form_state);
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
language_process_language_select($element);
CODE_BEFORE,
                    <<<'CODE_AFTER'
\Drupal::service(\Drupal\language\Hook\LanguageHooks::class)->processLanguageSelect($element);
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }
}
