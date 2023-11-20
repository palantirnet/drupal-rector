<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class WatchdogExceptionRector extends AbstractDrupalCoreRector
{
    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

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
        if (!$node instanceof Node\Expr\FuncCall || $this->getName($node) !== 'watchdog_exception') {
            return null;
        }

        $args = $node->getArgs();
        if (count($args) < 2) {
            return null;
        }

        // arg[5] could be sa link and that will need to be added to arg[3] as 'link' => arg[5]->value
        if (isset($args[5])) {
            assert($args[3]->value instanceof Node\Expr\Array_);
            $args[3]->value->items[] = new Node\Expr\ArrayItem($args[5]->value, new Node\Scalar\String_('link'));
            unset($args[5]);
        }

        $loggerNode = $this->nodeFactory->createStaticCall('Drupal', 'logger', [$args[0]]);
        $newArgs = [
            $loggerNode,
            $args[1],
        ];
        for ($i = 2; $i < count($args); ++$i) {
            $newArgs[] = $args[$i];
        }

        return $this->nodeFactory->createStaticCall('Drupal\Core\Utility\Error', 'logException', $newArgs);
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated watchdog_exception(\'update\', $exception) calls', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
watchdog_exception('update', $exception);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
use \Drupal\Core\Utility\Error;
$logger = \Drupal::logger('update');
Error::logException($logger, $exception);
CODE_AFTER
                ,
                [
                    new DrupalIntroducedVersionConfiguration('10.1.0'),
                ]
            ),
        ]);
    }
}
