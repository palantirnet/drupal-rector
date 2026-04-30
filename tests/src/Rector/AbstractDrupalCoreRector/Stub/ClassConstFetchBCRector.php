<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\AbstractDrupalCoreRector\Stub;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Test stub rector that replaces OldClass::OLD_CONST with \NewClass::NEW_CONST.
 *
 * Used to exercise the Expr → Expr BC-wrapping path in AbstractDrupalCoreRector
 * for node types that are not CallLike (ClassConstFetch is a plain Expr).
 */
class ClassConstFetchBCRector extends AbstractDrupalCoreRector
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
            Node\Expr\ClassConstFetch::class,
        ];
    }

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Node\Expr\ClassConstFetch);

        if (!$node->class instanceof Node\Name) {
            return null;
        }

        if ($this->getName($node->class) !== 'OldClass') {
            return null;
        }

        if (!$node->name instanceof Node\Identifier || $node->name->toString() !== 'OLD_CONST') {
            return null;
        }

        return new Node\Expr\ClassConstFetch(
            new Node\Name\FullyQualified('NewClass'),
            new Node\Identifier('NEW_CONST')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Test stub: replaces OldClass::OLD_CONST with \\NewClass::NEW_CONST, exercising the Expr BC-wrap path.', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$value = OldClass::OLD_CONST;
CODE_BEFORE,
                <<<'CODE_AFTER'
$value = \NewClass::NEW_CONST;
CODE_AFTER,
                [
                    new DrupalIntroducedVersionConfiguration('10.1.0'),
                ]
            ),
        ]);
    }
}
