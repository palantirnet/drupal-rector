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
 * Replaces deprecated CommentManagerInterface::getCountNewComments() calls.
 *
 * @see https://www.drupal.org/node/3551729
 */
final class ReplaceCommentManagerGetCountNewCommentsRector extends AbstractDrupalCoreRector
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

        if (!$this->isName($node->name, 'getCountNewComments')) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\comment\CommentManagerInterface'))) {
            return null;
        }

        $service = new Node\Expr\StaticCall(
            new Node\Name\FullyQualified('Drupal'),
            'service',
            [new Node\Arg(new Node\Expr\ClassConstFetch(new Node\Name\FullyQualified('Drupal\history\HistoryManager'), 'class'))]
        );

        return new Node\Expr\MethodCall($service, 'getCountNewComments', $node->args);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replaces deprecated CommentManagerInterface::getCountNewComments() with HistoryManager service', [
            new ConfiguredCodeSample(
                <<<'CODE_BEFORE'
$this->commentManager->getCountNewComments($entity);
CODE_BEFORE,
                <<<'CODE_AFTER'
\Drupal::service(\Drupal\history\HistoryManager::class)->getCountNewComments($entity);
CODE_AFTER,
                [new DrupalIntroducedVersionConfiguration('11.3.0')]
            ),
        ]);
    }
}
