<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Scalar\Int_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT with literal 20.
 *
 * Deprecated in drupal:11.4.0, removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3316878
 */
final class ReplaceEntityReferenceRecursiveLimitRector extends AbstractDrupalCoreRector
{
    private const TARGET_CLASSES = [
        'EntityReferenceEntityFormatter',
        'Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter',
    ];

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

    public function getNodeTypes(): array
    {
        return [ClassConstFetch::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof ClassConstFetch);

        if (!$this->isName($node->name, 'RECURSIVE_RENDER_LIMIT')) {
            return null;
        }

        foreach (self::TARGET_CLASSES as $class) {
            if ($this->isName($node->class, $class)) {
                return new Int_(20);
            }
        }

        return null;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT with literal 20 (drupal:11.4.0)', [
            new ConfiguredCodeSample(
                'if ($count > EntityReferenceEntityFormatter::RECURSIVE_RENDER_LIMIT) {}',
                'if ($count > 20) {}',
                [new DrupalIntroducedVersionConfiguration('11.4.0')]
            ),
        ]);
    }
}
