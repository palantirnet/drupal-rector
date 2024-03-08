<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use Rector\PhpParser\Node\Value\ValueResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class SystemTimeZonesRector extends AbstractDrupalCoreRector
{
    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    /**
     * @var ValueResolver
     */
    private ValueResolver $valueResolver;

    public function __construct(ValueResolver $valueResolver)
    {
        $this->valueResolver = $valueResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [
            Node\Expr\FuncCall::class,
        ];
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!($value instanceof DrupalIntroducedVersionConfiguration)) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)
    {
        if (!$node instanceof Node\Expr\FuncCall || $this->getName($node) !== 'system_time_zones') {
            return null;
        }

        $args = $node->getArgs();

        if (count($args) == 2 && $args[1]->value instanceof ConstFetch && $this->valueResolver->isTrue($args[1]->value)) {
            return $this->nodeFactory->createStaticCall('Drupal\Core\Datetime\TimeZoneFormHelper', 'getOptionsListByRegion');
        }

        if (count($args) == 0) {
            return $this->nodeFactory->createStaticCall('Drupal\Core\Datetime\TimeZoneFormHelper', 'getOptionsList');
        }

        if (count($args) <= 2) {
            return $this->nodeFactory->createStaticCall('Drupal\Core\Datetime\TimeZoneFormHelper', 'getOptionsList', [$args[0]]);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated system_time_zones() calls', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
system_time_zones();
system_time_zones(FALSE, TRUE);
system_time_zones(NULL, FALSE);
system_time_zones(TRUE, FALSE);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList();
\Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsListByRegion();
\Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList(NULL);
\Drupal\Core\Datetime\TimeZoneFormHelper::getOptionsList(TRUE);
CODE_AFTER
                ,
                [
                    new DrupalIntroducedVersionConfiguration('10.1.0'),
                ]
            ),
        ]);
    }
}
