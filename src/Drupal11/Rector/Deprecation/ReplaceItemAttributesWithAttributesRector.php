<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use DrupalRector\Contract\VersionedConfigurationInterface;
use DrupalRector\Rector\AbstractDrupalCoreRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Scalar\String_;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the deprecated '#item_attributes' key with '#attributes' in render
 * arrays that use the 'image_formatter' or 'responsive_image_formatter' theme
 * hooks.
 *
 * The '#item_attributes' property is deprecated in drupal:11.4.0 and removed in
 * drupal:12.0.0. The '#attributes' variable was only added to these theme hooks
 * in 11.4.0, so a plain rename silently drops the attributes on Drupal < 11.4.
 * The transformation is therefore BC-wrapped: the new key is used on Drupal
 * >= 11.4.0 and the original key is preserved on older versions.
 *
 * @see https://www.drupal.org/node/3554447
 * @see https://www.drupal.org/node/3554585
 */
final class ReplaceItemAttributesWithAttributesRector extends AbstractDrupalCoreRector
{
    private const AFFECTED_THEME_HOOKS = [
        'image_formatter',
        'responsive_image_formatter',
    ];

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
            "Replace deprecated '#item_attributes' with '#attributes' in image_formatter and responsive_image_formatter render arrays",
            [
                new ConfiguredCodeSample(
                    <<<'CODE_BEFORE'
$element = [
    '#theme' => 'image_formatter',
    '#item' => $item,
    '#item_attributes' => ['class' => ['my-image']],
];
CODE_BEFORE,
                    <<<'CODE_AFTER'
$element = [
    '#theme' => 'image_formatter',
    '#item' => $item,
    '#attributes' => ['class' => ['my-image']],
];
CODE_AFTER,
                    [new DrupalIntroducedVersionConfiguration('11.4.0')]
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Array_::class];
    }

    protected function refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration): ?Node
    {
        assert($node instanceof Array_);
        if (!$this->isImageFormatterArray($node)) {
            return null;
        }

        $changed = false;
        $cloned = clone $node;
        foreach ($cloned->items as $index => $item) {
            if (!$item->key instanceof String_ || $item->key->value !== '#item_attributes') {
                continue;
            }
            $newItem = clone $item;
            $newItem->key = new String_('#attributes');
            $cloned->items[$index] = $newItem;
            $changed = true;
        }

        return $changed ? $cloned : null;
    }

    /**
     * Determines whether the array is a render array for an affected theme hook.
     */
    private function isImageFormatterArray(Array_ $array): bool
    {
        foreach ($array->items as $item) {
            if (!$item->key instanceof String_ || $item->key->value !== '#theme') {
                continue;
            }
            if (!$item->value instanceof String_) {
                continue;
            }
            if (in_array($item->value->value, self::AFFECTED_THEME_HOOKS, true)) {
                return true;
            }
        }

        return false;
    }
}
