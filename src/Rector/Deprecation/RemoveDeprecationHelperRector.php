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
$pass = user_password();
$shorter_pass = user_password(8);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$pass = \Drupal::service('password_generator')->generate();
$shorter_pass = \Drupal::service('password_generator')->generate(8);
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

        foreach ($this->configuration as $configuration) {
            $introducedVersion = (string) $node->getArgs()[1]->value->value;
            $introducedVersionParts = explode('.', $introducedVersion);
            if ((int) array_shift($introducedVersionParts) + 1 !== $configuration->getMajorVersion()) {
                continue;
            }

            $newCall = $node->getArgs()[3]->value;
            if ($newCall instanceof Node\Expr\ArrowFunction) {
                return $newCall->expr;
            }
        }

        return null;
    }
}
