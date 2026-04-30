<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated ThemeHandlerInterface::rebuildThemeData() with the extension.list.theme service.
 *
 * @see https://www.drupal.org/node/3571068
 */
final class ReplaceRebuildThemeDataRector extends AbstractDrupalCoreRector
{
    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
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

    public function getNodeTypes(): array
    {
        return [Node\Expr\MethodCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        if (!$node instanceof Node\Expr\MethodCall) {
            return null;
        }

        if (!$this->isName($node->name, 'rebuildThemeData')) {
            return null;
        }

        if (!empty($node->args)) {
            return null;
        }

        $service = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            'service',
            [new Node\Arg(new Node\Scalar\String_('extension.list.theme'))]
        );

        $reset = new Node\Expr\MethodCall($service, 'reset');

        return new Node\Expr\MethodCall($reset, 'getList');
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition("Replaces removed ThemeHandlerInterface::rebuildThemeData() with \\Drupal::service('extension.list.theme')->reset()->getList()", [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->themeHandler->rebuildThemeData();
CODE_BEFORE,
                <<<'CODE_AFTER'
\Drupal::service('extension.list.theme')->reset()->getList();
CODE_AFTER,
                [new DrupalIntroducedVersionConfiguration('10.3.0')]
            ),
        ]);
    }
}
