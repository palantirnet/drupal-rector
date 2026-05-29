<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated Number::alphadecimalToInt(null/'') calls with 0.
 *
 * Passing null or empty string is deprecated in drupal:11.2.0 and removed
 * in drupal:12.0.0. Both values always returned 0.
 *
 * @see https://www.drupal.org/node/3442810
 * @see https://www.drupal.org/node/3494472
 */
final class ReplaceAlphadecimalToIntNullRector extends AbstractDrupalCoreRector
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
            "Replace deprecated Number::alphadecimalToInt(null/'') calls with 0",
            [
                new ConfiguredCodeSample(
                    'Number::alphadecimalToInt(NULL);',
                    '0;',
                    [new DrupalIntroducedVersionConfiguration('11.2.0')]
                ),
                new ConfiguredCodeSample(
                    "Number::alphadecimalToInt('');",
                    '0;',
                    [new DrupalIntroducedVersionConfiguration('11.2.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof StaticCall);
        if (!$this->isName($node->name, 'alphadecimalToInt')) {
            return null;
        }
        if (!$this->isObjectType($node->class, new ObjectType('Drupal\Component\Utility\Number'))) {
            return null;
        }
        if (count($node->args) !== 1) {
            return null;
        }
        $arg = $node->args[0];
        if (!$arg instanceof Arg) {
            return null;
        }
        $value = $arg->value;

        if ($value instanceof ConstFetch
            && strtolower($this->getName($value->name)) === 'null'
        ) {
            return new LNumber(0);
        }

        if ($value instanceof String_ && $value->value === '') {
            return new LNumber(0);
        }

        return null;
    }
}
