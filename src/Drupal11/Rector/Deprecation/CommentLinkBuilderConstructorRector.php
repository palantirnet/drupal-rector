<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Removes the deprecated $module_handler and $entity_type_manager arguments
 * from CommentLinkBuilder::__construct().
 *
 * The 5-argument form is rewritten to the new 3-argument form introduced in
 * Drupal 11.3.0. The $module_handler (arg 2) and $entity_type_manager (arg 4)
 * parameters were deprecated and removed; $string_translation shifts from
 * position 3 to position 2.
 *
 * Only rewrites calls with exactly 5 positional arguments. Named arguments or
 * calls with a different argument count are left untouched.
 *
 * @see https://www.drupal.org/node/3544308
 * @see https://www.drupal.org/node/3544527
 */
class CommentLinkBuilderConstructorRector extends AbstractDrupalCoreRector
{
    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    // TODO PHPSTAN_MESSAGES CommentLinkBuilderConstructorRector:
    // CommentLinkBuilder::__construct() is not annotated @deprecated — only
    // passing the (now removed) $module_handler/$entity_type_manager arguments
    // triggers a runtime trigger_error in core, keyed on the argument *types*.
    // PHPStan emits no static deprecation message for this call shape, so
    // upgrade_status cannot match this rector via the standard $rector_covered
    // lookup. Verified live against the installed Drupal 11.3 core constructor.
    public const PHPSTAN_MESSAGES = [];

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
            'Remove deprecated $module_handler and $entity_type_manager arguments from CommentLinkBuilder::__construct()',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
new \Drupal\comment\CommentLinkBuilder($currentUser, $commentManager, $moduleHandler, $stringTranslation, $entityTypeManager);
CODE_BEFORE,
                    <<<'CODE_AFTER'
new \Drupal\comment\CommentLinkBuilder($currentUser, $commentManager, $stringTranslation);
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.3.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof New_);
        if (!$node->class instanceof Name) {
            return null;
        }
        if (!$this->isName($node->class, 'Drupal\comment\CommentLinkBuilder')) {
            return null;
        }
        if (count($node->args) !== 5) {
            return null;
        }

        // Old signature: ($current_user, $comment_manager, $module_handler, $string_translation, $entity_type_manager)
        // New signature: ($current_user, $comment_manager, $string_translation)
        $cloned = clone $node;
        $cloned->args = [$node->args[0], $node->args[1], $node->args[3]];

        return $cloned;
    }
}
