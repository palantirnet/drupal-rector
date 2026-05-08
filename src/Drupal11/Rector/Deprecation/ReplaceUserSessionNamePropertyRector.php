<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated $userSession->name property read with $userSession->getAccountName().
 *
 * Deprecated in drupal:11.3.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3513856
 * @see https://www.drupal.org/node/3513877
 */
final class ReplaceUserSessionNamePropertyRector extends AbstractDrupalCoreRector
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

    public function getNodeTypes(): array
    {
        return [Node\Expr\PropertyFetch::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Node\Expr\PropertyFetch);

        if (!$this->isName($node->name, 'name')) {
            return null;
        }

        // Skip $this->name: protected property access within UserSession is
        // not deprecated and must not be rewritten to avoid infinite recursion
        // (UserSession::getAccountName() itself reads $this->name).
        if ($node->var instanceof Variable && $this->getName($node->var) === 'this') {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Session\UserSession'))) {
            return null;
        }

        return new Node\Expr\MethodCall($node->var, new Node\Identifier('getAccountName'));
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated $userSession->name property read with $userSession->getAccountName() (drupal:11.3.0)', [
            new ConfiguredCodeSample(
                '$name = $userSession->name;',
                '$name = $userSession->getAccountName();',
                [new DrupalIntroducedVersionConfiguration('11.3.0')]
            ),
        ]);
    }
}
