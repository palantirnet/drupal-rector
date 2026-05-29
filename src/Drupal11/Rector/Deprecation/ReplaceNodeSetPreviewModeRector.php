<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated DRUPAL_DISABLED/OPTIONAL/REQUIRED constants and integer literals
 * in NodeTypeInterface::setPreviewMode() calls with NodePreviewMode enum cases.
 *
 * Deprecated in drupal:11.3.0, removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3538277
 * @see https://www.drupal.org/node/3538666
 */
final class ReplaceNodeSetPreviewModeRector extends AbstractDrupalCoreRector
{
    private const CONST_TO_ENUM = [
        'DRUPAL_DISABLED' => 'Disabled',
        'DRUPAL_OPTIONAL' => 'Optional',
        'DRUPAL_REQUIRED' => 'Required',
    ];

    private const INT_TO_ENUM = [
        0 => 'Disabled',
        1 => 'Optional',
        2 => 'Required',
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
        return [Node\Expr\MethodCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Node\Expr\MethodCall);

        if (!$this->isName($node->name, 'setPreviewMode')) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\node\NodeTypeInterface'))) {
            return null;
        }

        if (count($node->args) !== 1) {
            return null;
        }

        $argValue = $node->args[0]->value;
        $enumCase = null;

        if ($argValue instanceof Node\Expr\ConstFetch) {
            $constName = $this->getName($argValue);
            if ($constName !== null && isset(self::CONST_TO_ENUM[$constName])) {
                $enumCase = self::CONST_TO_ENUM[$constName];
            }
        }

        if ($enumCase === null && $argValue instanceof Node\Scalar\LNumber) {
            $enumCase = self::INT_TO_ENUM[$argValue->value] ?? null;
        }

        if ($enumCase === null) {
            return null;
        }

        $cloned = clone $node;
        $cloned->args[0] = new Node\Arg(
            new Node\Expr\ClassConstFetch(
                new Node\Name\FullyQualified('Drupal\node\NodePreviewMode'),
                $enumCase
            )
        );

        return $cloned;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated DRUPAL_DISABLED/OPTIONAL/REQUIRED constants and integer literals in setPreviewMode() with NodePreviewMode enum cases (drupal:11.3.0)', [
            new ConfiguredCodeSample(
                '$nodeType->setPreviewMode(DRUPAL_DISABLED);',
                '$nodeType->setPreviewMode(\\Drupal\\node\\NodePreviewMode::Disabled);',
                [new DrupalIntroducedVersionConfiguration('11.3.0')]
            ),
        ]);
    }
}
