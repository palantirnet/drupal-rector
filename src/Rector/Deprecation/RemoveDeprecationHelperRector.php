<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\RemoveDeprecationHelperConfiguration;
use PhpParser\Node;
use PhpParser\NodeDumper;
use Rector\Core\Contract\Rector\ConfigurableRectorInterface;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class RemoveDeprecationHelperRector extends AbstractRector implements ConfigurableRectorInterface
{

    /**
     * @var array|RemoveDeprecationHelperConfiguration[]
     */
    private array $configuration;

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated user_password() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
$settings = [];
$filename = 'simple_filename.yaml';
drupal_rewrite_settings($settings, $filename);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$settings = [];
$filename = 'simple_filename.yaml';
DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => drupal_rewrite_settings($settings, $filename), fn() => SettingsEditor::rewrite($filename, $settings));
CODE_AFTER
            )
        ]);
    }


    public function configure(array $configuration): void {
        foreach ($configuration as $value) {
            if (!($value instanceof RemoveDeprecationHelperConfiguration)) {
                throw new \InvalidArgumentException(sprintf(
                    'Each configuration item must be an instance of "%s"',
                    RemoveDeprecationHelperConfiguration::class
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
            $introducedVersionParts = explode('.', $introducedVersion);
            if ((int) array_shift($introducedVersionParts) + 1 !== $configuration->getMajorVersion()) {
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
