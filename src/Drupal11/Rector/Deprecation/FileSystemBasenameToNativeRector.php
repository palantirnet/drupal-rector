<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PHPStan\Type\ObjectType;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated FileSystemInterface::basename() with native basename().
 *
 * FileSystemInterface::basename() is deprecated in drupal:11.3.0 and removed
 * in drupal:13.0.0. PHP native basename() is identical on PHP 8.x+.
 *
 * @see https://www.drupal.org/node/3530461
 * @see https://www.drupal.org/node/3530869
 */
final class FileSystemBasenameToNativeRector extends AbstractDrupalCoreRector
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
            'Replace deprecated FileSystemInterface::basename() calls with PHP native basename()',
            [
                new ConfiguredCodeSample(
                    '$fileSystem->basename($uri, $suffix);',
                    'basename($uri, $suffix);',
                    [new DrupalIntroducedVersionConfiguration('11.3.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof MethodCall);
        if (!$this->isName($node->name, 'basename')) {
            return null;
        }

        $isFileSystem = false;
        foreach (['Drupal\Core\File\FileSystemInterface', 'Drupal\Core\File\FileSystem'] as $class) {
            if ($this->isObjectType($node->var, new ObjectType($class))) {
                $isFileSystem = true;
                break;
            }
        }

        if (!$isFileSystem) {
            return null;
        }

        return new FuncCall(new Name('basename'), $node->getArgs());
    }
}
