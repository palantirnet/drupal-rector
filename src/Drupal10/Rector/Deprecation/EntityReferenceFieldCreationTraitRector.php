<?php

declare(strict_types=1);

namespace DrupalRector\Drupal10\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\NodeDumper;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class EntityReferenceFieldCreationTraitRector extends AbstractDrupalCoreRector
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
            Node\Stmt\Class_::class,
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

    /**
     * @phpstan-param \PhpParser\Node\Stmt\Class_ $node
     *
     * @param \PhpParser\Node $node
     * @param \DrupalRector\Contract\VersionedConfigurationInterface $configuration
     *
     * @return Node|Node[]|null
     */
    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)
    {
        if (!$node instanceof Node\Stmt\Class_) {
            return null;
        }
        /** @var \PhpParser\Node\Stmt\Class_ $node */
        $reflection = $node->getTraitUses();

        foreach ($reflection as $traitUse) {
            $traits = $traitUse->traits;
            foreach ($traits as $key => $trait) {
                echo PHP_EOL . $trait->toString();
                if ($trait->toString() === 'Drupal\Tests\field\Traits\EntityReferenceTestTrait') {
                    echo PHP_EOL . 'Found it! ' . $key . ' ' . $trait->toString() . PHP_EOL;
                    echo (new NodeDumper())->dump($node);
                    $stmt = new Node\Stmt\If_(
                        new Node\Expr\FuncCall(
                            new Node\Name('class_exists'),
                            [
                                new Node\Arg(
                                    new Node\Scalar\String_('Drupal\Core\Field\EntityReferenceFieldCreationTrait')
                                ),
                            ]
                        ),
                        [
                            'stmts' => [
                                new Node\Stmt\Expression(
                                    new Node\Expr\FuncCall(
                                        new Node\Name('class_alias'),
                                        [
                                            new Node\Arg(
                                                new Node\Scalar\String_('Drupal\Core\Field\EntityReferenceTestTrait')
                                            ),
                                            new Node\Arg(
                                                new Node\Scalar\String_('Drupal\Core\Field\EntityReferenceFieldCreationTrait')
                                            ),
                                        ]
                                    )
                                ),
                            ],
                        ]
                    );


                    return [
                        $stmt,
                        $node
                    ];
                }
            }
        }

        return null;

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
