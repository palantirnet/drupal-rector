<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated Views::pluginManager() and Views::handlerManager() calls.
 *
 * Deprecated in drupal:11.4.0, removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3566424
 * @see https://www.drupal.org/node/3566982
 */
final class ViewsPluginHandlerManagerRector extends AbstractDrupalCoreRector
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
        return [Node\Expr\StaticCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        if (!$node instanceof Node\Expr\StaticCall) {
            return null;
        }

        if (!$this->isName($node->class, 'Drupal\views\Views')) {
            return null;
        }

        $methodName = $this->getName($node->name);
        if ($methodName !== 'pluginManager' && $methodName !== 'handlerManager') {
            return null;
        }

        if (count($node->args) === 0) {
            return null;
        }

        $arg = $node->args[0];
        if (!$arg instanceof Node\Arg) {
            return null;
        }

        $typeExpr = $arg->value;
        $drupalClass = new Node\Name\FullyQualified('Drupal');

        if ($typeExpr instanceof Node\Scalar\String_) {
            return new Node\Expr\StaticCall(
                $drupalClass,
                'service',
                [new Node\Arg(new Node\Scalar\String_('plugin.manager.views.'.$typeExpr->value))]
            );
        }

        $serviceLocatorCall = new Node\Expr\StaticCall(
            $drupalClass,
            'service',
            [new Node\Arg(new Node\Scalar\String_('views.plugin_managers'))]
        );

        return new Node\Expr\MethodCall($serviceLocatorCall, 'get', [new Node\Arg($typeExpr)]);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replaces deprecated Views::pluginManager() and Views::handlerManager() with \\Drupal::service() equivalents', [
            new ConfiguredCodeSample(
                "Views::handlerManager('filter');\nViews::pluginManager(\$type);",
                "\\Drupal::service('plugin.manager.views.filter');\n\\Drupal::service('views.plugin_managers')->get(\$type);",
                [new DrupalIntroducedVersionConfiguration('11.4.0')]
            ),
        ]);
    }
}
