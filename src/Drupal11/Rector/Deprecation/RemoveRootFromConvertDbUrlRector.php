<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes the deprecated string $root argument from Database::convertDbUrlToConnectionInfo().
 *
 * Deprecated in drupal:11.3.0. The $root parameter is no longer needed.
 * Any third $include_test_drivers argument is shifted to position two.
 *
 * @see https://www.drupal.org/node/3522513
 * @see https://www.drupal.org/node/3511287
 */
final class RemoveRootFromConvertDbUrlRector extends AbstractDrupalCoreRector
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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove deprecated string $root argument from Database::convertDbUrlToConnectionInfo()',
            [
                new ConfiguredCodeSample(
                    'Database::convertDbUrlToConnectionInfo($url, $this->root, TRUE);',
                    'Database::convertDbUrlToConnectionInfo($url, TRUE);',
                    [new DrupalIntroducedVersionConfiguration('11.3.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof StaticCall);
        if (!$this->isName($node->class, 'Drupal\Core\Database\Database')) {
            return null;
        }
        if (!$this->isName($node->name, 'convertDbUrlToConnectionInfo')) {
            return null;
        }
        if (count($node->args) < 2) {
            return null;
        }

        $secondArg = $node->args[1];
        if (!$secondArg instanceof Arg) {
            return null;
        }
        $secondArgValue = $secondArg->value;

        if ($secondArgValue instanceof ConstFetch) {
            $constName = strtolower((string) $secondArgValue->name);
            if ($constName === 'true' || $constName === 'false' || $constName === 'null') {
                return null;
            }
        } elseif ($secondArgValue instanceof Variable) {
            return null;
        } elseif (
            !$secondArgValue instanceof String_
            && !$secondArgValue instanceof PropertyFetch
            && !$secondArgValue instanceof NullsafePropertyFetch
            && !$secondArgValue instanceof FuncCall
            && !$secondArgValue instanceof StaticPropertyFetch
            && !$secondArgValue instanceof MethodCall
        ) {
            return null;
        }

        $cloned = clone $node;
        array_splice($cloned->args, 1, 1);

        return $cloned;
    }
}
