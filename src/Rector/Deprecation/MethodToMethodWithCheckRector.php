<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\MethodToMethodWithCheckConfiguration;
use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated method calls with a new method.
 *
 * What is covered:
 * - Changes the name of the method when the receiver type can be inferred.
 * - Wraps in DeprecationHelper::backwardsCompatibleCall when the introduced
 *   version warrants BC support.
 */
class MethodToMethodWithCheckRector extends AbstractDrupalCoreRector
{
    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof MethodToMethodWithCheckConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', MethodToMethodWithCheckConfiguration::class));
            }
        }

        parent::configure($configuration);
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeTypes(): array
    {
        return [Node\Expr\MethodCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Node\Expr\MethodCall);
        assert($configuration instanceof MethodToMethodWithCheckConfiguration);

        if ($this->getName($node->name) !== $configuration->getDeprecatedMethodName()) {
            return null;
        }

        $callerType = $this->nodeTypeResolver->getType($node->var);
        $expectedType = new ObjectType($configuration->getClassName());

        $isSuperOf = $expectedType->isSuperTypeOf($callerType);
        if (!$isSuperOf->yes() && !$isSuperOf->maybe()) {
            return null;
        }

        $newNode = clone $node;
        $newNode->name = new Node\Identifier($configuration->getMethodName());

        return $newNode;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated MetadataBag::clearCsrfTokenSeed() calls, used in Drupal 8 and 9 deprecations', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$metadata_bag = new \Drupal\Core\Session\MetadataBag(new \Drupal\Core\Site\Settings([]));
$metadata_bag->clearCsrfTokenSeed();
CODE_BEFORE,
                <<<'CODE_AFTER'
$metadata_bag = new \Drupal\Core\Session\MetadataBag(new \Drupal\Core\Site\Settings([]));
$metadata_bag->stampNew();
CODE_AFTER,
                [
                    new MethodToMethodWithCheckConfiguration(
                        'Drupal\Core\Session\MetadataBag',
                        'clearCsrfTokenSeed',
                        'stampNew',
                        '9.2.0',
                    ),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$url = $entity->urlInfo();
CODE_BEFORE,
                <<<'CODE_AFTER'
$url = $entity->toUrl();
CODE_AFTER,
                [
                    new MethodToMethodWithCheckConfiguration('Drupal\Core\Entity\EntityInterface', 'urlInfo', 'toUrl', '8.0.0'),
                ]
            ),
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
/* @var \Drupal\node\Entity\Node $node */
$node = \Drupal::entityTypeManager()->getStorage('node')->load(123);
$entity_type = $node->getEntityType();
$entity_type->getLowercaseLabel();
CODE_BEFORE,
                <<<'CODE_AFTER'
/* @var \Drupal\node\Entity\Node $node */
$node = \Drupal::entityTypeManager()->getStorage('node')->load(123);
$entity_type = $node->getEntityType();
$entity_type->getSingularLabel();
CODE_AFTER,
                [
                    new MethodToMethodWithCheckConfiguration('Drupal\Core\Entity\EntityTypeInterface', 'getLowercaseLabel', 'getSingularLabel', '8.8.0'),
                ]
            ),
        ]);
    }
}
