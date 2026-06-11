<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the deprecated options-array argument of UploadedFileConstraint with
 * explicit named constructor arguments.
 *
 * Symfony validator 7.4 deprecated passing an associative array as the first
 * argument to Constraint constructors. Drupal's UploadedFileConstraint follows
 * suit: passing an options array is deprecated in drupal:11.4.0 and removed in
 * drupal:12.0.0. The named-argument constructor was introduced in the same
 * commit as the deprecation, so the new form would fatal ("Unknown named
 * parameter") on Drupal < 11.4. The transformation is therefore BC-wrapped: the
 * named-argument form is used on Drupal >= 11.4.0 and the original options-array
 * form is preserved on older versions.
 *
 * @see https://www.drupal.org/node/3561135
 * @see https://www.drupal.org/node/3554746
 */
final class UploadedFileConstraintArrayOptionsToNamedArgsRector extends AbstractDrupalCoreRector
{
    /**
     * @var array|DrupalIntroducedVersionConfiguration[]
     */
    protected array $configuration;

    // TODO PHPSTAN_MESSAGES UploadedFileConstraintArrayOptionsToNamedArgsRector:
    // Neither UploadedFileConstraint nor its constructor is annotated
    // @deprecated — only passing an array as the first argument triggers a
    // runtime trigger_error in core (keyed on is_array($options)). PHPStan
    // emits no static deprecation message for this call shape, so
    // upgrade_status cannot match this rector via the standard $rector_covered
    // lookup. Verified live against the installed Drupal 11.4 core constructor.
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
            'Replace the deprecated options-array argument of UploadedFileConstraint with named constructor arguments.',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
$constraint = new UploadedFileConstraint(['maxSize' => 1024000]);
CODE_BEFORE,
                    <<<'CODE_AFTER'
$constraint = new UploadedFileConstraint(maxSize: 1024000);
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
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

        // Match both the imported short name and the fully-qualified name.
        if (!$this->isNames($node->class, [
            'UploadedFileConstraint',
            'Drupal\\file\\Validation\\Constraint\\UploadedFileConstraint',
        ])) {
            return null;
        }

        // Nothing to do when there are no arguments at all.
        if (0 === count($node->args)) {
            return null;
        }

        $firstArg = $node->args[0];

        // Skip when the first argument is already named, or is not a plain Arg
        // node (e.g. a variadic/spread placeholder).
        if (!$firstArg instanceof Arg || null !== $firstArg->name) {
            return null;
        }

        // The first argument must be an inline array literal.
        if (!$firstArg->value instanceof Array_) {
            return null;
        }

        $array = $firstArg->value;

        // Convert every array item into an explicit named argument. Bail out if
        // any item lacks a plain string-literal key — we cannot safely derive a
        // named argument in that case.
        $namedArgs = [];
        foreach ($array->items as $item) {
            if (!$item->key instanceof String_) {
                return null;
            }
            $namedArg = new Arg($item->value);
            $namedArg->name = new Identifier($item->key->value);
            $namedArgs[] = $namedArg;
        }

        // Build the replacement node, preserving any positional args (groups,
        // payload, …) that follow the options array. The original $node is left
        // untouched so the base class can emit it as the BC fallback.
        $new = clone $node;
        $new->args = array_merge($namedArgs, array_slice($node->args, 1));

        return $new;
    }
}
