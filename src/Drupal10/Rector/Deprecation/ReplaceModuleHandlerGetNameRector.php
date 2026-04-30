<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated ModuleHandlerInterface::getName() calls.
 *
 * @see https://www.drupal.org/node/3571063
 */
final class ReplaceModuleHandlerGetNameRector extends AbstractDrupalCoreRector
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

        if (!$this->isName($node->name, 'getName')) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Extension\ModuleHandlerInterface'))) {
            return null;
        }

        $service = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            'service',
            [new Node\Arg(new Node\Scalar\String_('extension.list.module'))]
        );

        return new Node\Expr\MethodCall($service, 'getName', $node->args);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition("Replaces deprecated ModuleHandlerInterface::getName() with \\Drupal::service('extension.list.module')->getName()", [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->moduleHandler->getName($module);
CODE_BEFORE,
                <<<'CODE_AFTER'
\Drupal::service('extension.list.module')->getName($module);
CODE_AFTER,
                [new DrupalIntroducedVersionConfiguration('10.3.0')]
            ),
        ]);
    }
}
