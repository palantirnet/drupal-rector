<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated filter procedural functions with plugin manager calls.
 *
 * Targets _filter_autop(), _filter_html_escape(), and
 * _filter_html_image_secure_process() deprecated in drupal:11.4.0.
 * Each is rewritten to the equivalent plugin.manager.filter createInstance()
 * chain.
 *
 * @see https://www.drupal.org/node/3226806
 * @see https://www.drupal.org/node/3566774
 */
final class DeprecatedFilterFunctionsRector extends AbstractDrupalCoreRector
{
    /**
     * Maps deprecated function name to filter plugin ID.
     *
     * @var array<string, string>
     */
    private const FUNCTION_TO_PLUGIN_ID = [
        '_filter_autop' => 'filter_autop',
        '_filter_html_escape' => 'filter_html_escape',
        '_filter_html_image_secure_process' => 'filter_html_image_secure',
    ];

    /** @var DrupalIntroducedVersionConfiguration[] */
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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated _filter_autop(), _filter_html_escape(), and _filter_html_image_secure_process() with plugin manager calls.',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
_filter_autop($text)
CODE_BEFORE,
                    <<<'CODE_AFTER'
\Drupal::service('plugin.manager.filter')->createInstance('filter_autop')->process($text, \Drupal::languageManager()->getCurrentLanguage()->getId())->getProcessedText()
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof FuncCall);

        if (!$node->name instanceof Name) {
            return null;
        }

        $funcName = $this->getName($node->name);

        if (!isset(self::FUNCTION_TO_PLUGIN_ID[$funcName])) {
            return null;
        }

        if (count($node->args) < 1) {
            return null;
        }

        $pluginId = self::FUNCTION_TO_PLUGIN_ID[$funcName];

        $textArg = $node->args[0];
        $textExpr = $textArg instanceof Arg ? $textArg->value : $textArg;

        // Build: \Drupal::languageManager()->getCurrentLanguage()->getId()
        $drupalLanguageManager = $this->nodeFactory->createStaticCall('Drupal', 'languageManager');
        $getCurrentLanguage = $this->nodeFactory->createMethodCall($drupalLanguageManager, 'getCurrentLanguage');
        $getLangcodeExpr = $this->nodeFactory->createMethodCall($getCurrentLanguage, 'getId');

        // Build: \Drupal::service('plugin.manager.filter')
        $drupalService = $this->nodeFactory->createStaticCall('Drupal', 'service', [
            new String_('plugin.manager.filter'),
        ]);

        // ->createInstance('filter_autop') (or other plugin id)
        $createInstance = $this->nodeFactory->createMethodCall($drupalService, 'createInstance', [
            new String_($pluginId),
        ]);

        // ->process($text, $langcode)
        $process = $this->nodeFactory->createMethodCall($createInstance, 'process', [
            $textExpr,
            $getLangcodeExpr,
        ]);

        // ->getProcessedText()
        return $this->nodeFactory->createMethodCall($process, 'getProcessedText');
    }
}
