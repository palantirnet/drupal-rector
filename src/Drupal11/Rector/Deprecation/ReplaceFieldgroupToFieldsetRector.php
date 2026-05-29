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
 * Replaces '#type' => 'fieldgroup' with '#type' => 'fieldset' in render arrays.
 *
 * The Fieldgroup render element is deprecated in drupal:11.2.0 and removed in
 * drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3512254
 * @see https://www.drupal.org/node/3515272
 */
final class ReplaceFieldgroupToFieldsetRector extends AbstractDrupalCoreRector
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
            "Replace '#type' => 'fieldgroup' with '#type' => 'fieldset' in render arrays",
            [
                new ConfiguredCodeSample(
                    "\$form['account'] = ['#type' => 'fieldgroup', '#title' => \$this->t('Account settings')];",
                    "\$form['account'] = ['#type' => 'fieldset', '#title' => \$this->t('Account settings')];",
                    [new DrupalIntroducedVersionConfiguration('11.2.0')]
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
        $changed = false;
        $cloned = clone $node;
        foreach ($cloned->items as $index => $item) {
            if (!$item->key instanceof String_ || $item->key->value !== '#type') {
                continue;
            }
            if (!$item->value instanceof String_ || $item->value->value !== 'fieldgroup') {
                continue;
            }
            $newItem = clone $item;
            $newItem->value = new String_('fieldset');
            $cloned->items[$index] = $newItem;
            $changed = true;
        }

        return $changed ? $cloned : null;
    }
}
