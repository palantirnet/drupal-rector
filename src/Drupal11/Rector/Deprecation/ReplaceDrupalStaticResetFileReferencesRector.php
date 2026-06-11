<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces drupal_static_reset() for the deprecated file_get_file_references
 * static-cache keys with cache.memory tag invalidation.
 *
 * Both static-cache keys ('file_get_file_references' and
 * 'file_get_file_references:field_columns') were deprecated in drupal:11.4.0
 * when the file-reference lookup moved to the new FileReferenceResolver
 * service, which caches in the 'cache.memory' bin under the 'file_references'
 * cache tag instead of drupal_static().
 *
 * The replacement is BC-wrapped: on Drupal < 11.4.0 the 'file_references'
 * cache tag does not exist, so calling invalidateTags() there would be a
 * silent no-op instead of resetting the drupal_static() the old code path
 * actually uses. The DeprecationHelper wrapper therefore keeps the original
 * drupal_static_reset() call on older versions and only switches to the
 * cache.memory invalidation on drupal:11.4.0 and above.
 *
 * Calls to file_get_file_references() itself are intentionally left untouched:
 * its replacement FileReferenceResolver::getReferences() returns a Generator
 * of FileReferenceUsage objects rather than the old nested array, so callers
 * need manual refactoring. Named-argument and unpacked-argument forms of
 * drupal_static_reset() are also left for manual review.
 *
 * @see https://www.drupal.org/node/1452100
 * @see https://www.drupal.org/node/3573884
 */
class ReplaceDrupalStaticResetFileReferencesRector extends AbstractDrupalCoreRector
{
    /** @var DrupalIntroducedVersionConfiguration[] */
    protected array $configuration;

    // Both static keys were deprecated together (drupal:11.4.0).
    private const DEPRECATED_KEYS = [
        'file_get_file_references',
        'file_get_file_references:field_columns',
    ];

    public function configure(array $configuration): void
    {
        foreach ($configuration as $value) {
            if (!$value instanceof DrupalIntroducedVersionConfiguration) {
                throw new \InvalidArgumentException(sprintf('Each configuration item must be an instance of "%s"', DrupalIntroducedVersionConfiguration::class));
            }
        }
        parent::configure($configuration);
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    public function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof FuncCall);

        if (!$this->isName($node->name, 'drupal_static_reset')) {
            return null;
        }
        if (count($node->args) !== 1) {
            return null;
        }
        $firstArg = $node->args[0];
        if (!$firstArg instanceof Arg) {
            return null;
        }
        // Skip named and unpacked args — they are exotic enough to leave for
        // manual review.
        if ($firstArg->name !== null || $firstArg->unpack) {
            return null;
        }
        if (!$firstArg->value instanceof String_) {
            return null;
        }
        if (!in_array($firstArg->value->value, self::DEPRECATED_KEYS, true)) {
            return null;
        }

        return new MethodCall(
            new StaticCall(
                new FullyQualified('Drupal'),
                'service',
                [new Arg(new String_('cache.memory'))],
            ),
            'invalidateTags',
            [new Arg(new Array_([new ArrayItem(new String_('file_references'))]))],
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace drupal_static_reset() for file_get_file_references keys with cache tag invalidation',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
drupal_static_reset('file_get_file_references');
CODE_BEFORE,
                    <<<'CODE_AFTER'
\Drupal::service('cache.memory')->invalidateTags(['file_references']);
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }
}
