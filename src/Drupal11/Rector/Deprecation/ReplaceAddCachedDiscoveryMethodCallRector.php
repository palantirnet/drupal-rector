<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated addMethodCall('addCachedDiscovery', ...) calls on the plugin.cache_clearer service definition with the plugin_manager_cache_clear tag approach.
 *
 * @see https://www.drupal.org/node/3432827
 * @see https://www.drupal.org/node/3442229
 */
class ReplaceAddCachedDiscoveryMethodCallRector extends AbstractDrupalCoreRector
{
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

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof MethodCall);

        if (!$this->isName($node->name, 'addMethodCall')) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Symfony\Component\DependencyInjection\Definition'))) {
            return null;
        }

        $args = $node->getArgs();
        if (count($args) < 2) {
            return null;
        }

        $firstArg = $args[0]->value;
        if (!$firstArg instanceof String_ || $firstArg->value !== 'addCachedDiscovery') {
            return null;
        }

        $secondArg = $args[1]->value;
        if (!$secondArg instanceof Node\Expr\Array_) {
            return null;
        }

        $serviceIdNode = null;
        foreach ($secondArg->items as $item) {
            $itemValue = $item->value;
            if ($itemValue instanceof New_) {
                $className = $itemValue->class;
                if ($className instanceof Name) {
                    $shortName = $className->getLast();
                    if ($shortName === 'Reference' && count($itemValue->getArgs()) >= 1) {
                        $serviceIdNode = $itemValue->getArgs()[0]->value;
                        break;
                    }
                }
            }
        }

        if ($serviceIdNode === null) {
            return null;
        }

        $containerVar = new Variable('container');

        $getDefinitionCall = new MethodCall(
            $containerVar,
            'getDefinition',
            [new Arg($serviceIdNode)]
        );

        return new MethodCall(
            $getDefinitionCall,
            'addTag',
            [new Arg(new String_('plugin_manager_cache_clear'))]
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            "Replace deprecated \$def->addMethodCall('addCachedDiscovery', [new Reference(\$id)]) with \$container->getDefinition(\$id)->addTag('plugin_manager_cache_clear')",
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
$container->getDefinition('plugin.cache_clearer')
    ->addMethodCall('addCachedDiscovery', [new Reference('my.plugin.manager')]);
CODE_BEFORE,
                    <<<'CODE_AFTER'
$container->getDefinition('my.plugin.manager')->addTag('plugin_manager_cache_clear');
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.1.0')]
                ),
            ]
        );
    }
}
