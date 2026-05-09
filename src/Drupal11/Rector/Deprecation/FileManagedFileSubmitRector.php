<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the deprecated 'file_managed_file_submit' string callback with
 * [\Drupal\file\Element\ManagedFile::class, 'submit'] array callable.
 *
 * @see https://www.drupal.org/node/3534089
 * @see https://www.drupal.org/node/3534091
 */
final class FileManagedFileSubmitRector extends AbstractDrupalCoreRector
{
    private const DEPRECATED_FUNCTION = 'file_managed_file_submit';
    private const NEW_CLASS = 'Drupal\\file\\Element\\ManagedFile';
    private const NEW_METHOD = 'submit';

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
            "Replace deprecated 'file_managed_file_submit' string callback with [\\Drupal\\file\\Element\\ManagedFile::class, 'submit'] array callable",
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
$form['upload']['#submit'] = ['file_managed_file_submit'];
CODE_BEFORE,
                    <<<'CODE_AFTER'
$form['upload']['#submit'] = [[\Drupal\file\Element\ManagedFile::class, 'submit']];
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.3.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [String_::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof String_);

        if ($node->value !== self::DEPRECATED_FUNCTION) {
            return null;
        }

        return new Array_([
            new ArrayItem(
                new ClassConstFetch(
                    new FullyQualified(self::NEW_CLASS),
                    'class'
                )
            ),
            new ArrayItem(
                new String_(self::NEW_METHOD)
            ),
        ]);
    }
}
