<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DeprecationHelperRemoveConfiguration;
use PhpParser\Node;
use PhpParser\NodeDumper;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class DeprecationHelperRemoveRector extends AbstractRector implements ConfigurableRectorInterface
{

    /**
     * @var array|DeprecationHelperRemoveConfiguration[]
     */
    private array $configuration;

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Remove DeprecationHelper calls for versions before configured minimum requirement', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$settings = [];
$filename = 'simple_filename.yaml';
DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0', fn() => old_function(), fn() => new_function());
DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.5.0', fn() => drupal_rewrite_settings($settings, $filename), fn() => SettingsEditor::rewrite($filename, $settings));
DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => old_function(), fn() => new_function());
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$settings = [];
$filename = 'simple_filename.yaml';
drupal_rewrite_settings($settings, $filename);
DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => old_function(), fn() => new_function());
CODE_AFTER
                ,
                [
                    new DeprecationHelperRemoveConfiguration('10.5.0')
                ]
            )
        ]);
    }


    public function configure(array $configuration): void {
        foreach ($configuration as $value) {
            if (!($value instanceof DeprecationHelperRemoveConfiguration)) {
                throw new \InvalidArgumentException(sprintf(
                    'Each configuration item must be an instance of "%s"',
                    DeprecationHelperRemoveConfiguration::class
                ));
            }
        }

        $this->configuration = $configuration;
    }


    /**
     * @inheritdoc
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\StaticCall::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Expr\StaticCall);

        if($this->getName($node->name) !== 'backwardsCompatibleCall') {
            return null;
        }


        $args = $node->getArgs();
        foreach ($this->configuration as $configuration) {
            assert($args[1]->value instanceof Node\Scalar\String_);

            $introducedVersion = (string) $args[1]->value->value;
            if (version_compare($introducedVersion, $configuration->getMinimumRequirement(), '>=')) {
                continue;
            }

            $newCall = $args[3]->value;
            if ($newCall instanceof Node\Expr\ArrowFunction) {
                return $newCall->expr;
            }
        }

        return null;
    }
}
