<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes the deprecated boolean $has_trusted_data argument from save() calls.
 *
 * Passing any argument to Config::save() is deprecated in drupal:11.4.0 and
 * removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3347842
 * @see https://www.drupal.org/node/3348180
 */
final class RemoveConfigSaveTrustedDataArgRector extends AbstractDrupalCoreRector
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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated boolean $has_trusted_data argument from Config::save() calls',
            [
                new ConfiguredCodeSample(
                    '$config->save(TRUE);',
                    '$config->save();',
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof MethodCall);
        if (!$this->isName($node->name, 'save')) {
            return null;
        }
        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Config\Config'))) {
            return null;
        }
        if (count($node->args) !== 1) {
            return null;
        }
        $arg = $node->args[0];
        if (!$arg instanceof Arg) {
            return null;
        }
        if (!$arg->value instanceof ConstFetch) {
            return null;
        }
        $constName = strtolower((string) $arg->value->name);
        if ($constName !== 'true' && $constName !== 'false') {
            return null;
        }

        $cloned = clone $node;
        $cloned->args = [];

        return $cloned;
    }
}
