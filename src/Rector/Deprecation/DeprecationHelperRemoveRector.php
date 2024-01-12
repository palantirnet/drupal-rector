<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\ValueObject\DeprecationHelperRemoveConfiguration;
use PhpParser\Node;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class DeprecationHelperRemoveRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array|DeprecationHelperRemoveConfiguration[]
     */
    private array $configuration;

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove DeprecationHelper calls for versions before configured minimum requirement', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$settings = [];
$filename = 'simple_filename.yaml';
DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', fn() => new_function(), fn() => old_function());
DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => SettingsEditor::rewrite($filename, $settings), fn() => drupal_rewrite_settings($settings, $filename));
DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => new_function(), fn() => old_function());
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$settings = [];
$filename = 'simple_filename.yaml';
drupal_rewrite_settings($settings, $filename);
DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => new_function(), fn() => old_function());
CODE_AFTER
                ,
                [
                    new DeprecationHelperRemoveConfiguration('10.5.0'),
                ]
            ),
        ]);
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof DeprecationHelperRemoveConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DeprecationHelperRemoveConfiguration::class));
            }
        }

        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\StaticCall::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\StaticCall);

        if ($this->getName($node->name) !== 'backwardsCompatibleCall') {
            return null;
        }

        $args = $node->getArgs();
        foreach ($this->configuration as $configuration) {
            assert($args[1]->value instanceof Node\Scalar\String_);

            $introducedVersion = (string) $args[1]->value->value;
            if (version_compare($introducedVersion, $configuration->getMinimumRequirement(), '>=')) {
                continue;
            }

            $newCall = $args[2]->value;
            if ($newCall instanceof Node\Expr\ArrowFunction) {
                return $newCall->expr;
            }
        }

        return null;
    }
}
