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
 * Replaces deprecated SessionManager::delete() with UserSessionRepositoryInterface::deleteAll().
 *
 * @see https://www.drupal.org/node/3577376
 */
final class ReplaceSessionManagerDeleteRector extends AbstractDrupalCoreRector
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
        return [Node\Expr\MethodCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        if (!$node instanceof Node\Expr\MethodCall) {
            return null;
        }

        if (!$this->isName($node->name, 'delete')) {
            return null;
        }

        if (count($node->args) !== 1) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Core\Session\SessionManager'))) {
            return null;
        }

        $service = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            'service',
            [new Node\Arg(new Node\Expr\ClassConstFetch(new Node\Name\FullyQualified('Drupal\Core\Session\UserSessionRepositoryInterface'), 'class'))]
        );

        return new Node\Expr\MethodCall($service, 'deleteAll', [$node->args[0]]);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replaces deprecated SessionManager::delete($uid) with UserSessionRepositoryInterface service', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->sessionManager->delete($uid);
CODE_BEFORE,
                <<<'CODE_AFTER'
\Drupal::service(\Drupal\Core\Session\UserSessionRepositoryInterface::class)->deleteAll($uid);
CODE_AFTER,
                [new DrupalIntroducedVersionConfiguration('11.4.0')]
            ),
        ]);
    }
}
