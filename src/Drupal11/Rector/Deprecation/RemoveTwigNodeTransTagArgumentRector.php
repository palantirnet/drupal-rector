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
 * Removes the deprecated 6th $tag constructor argument from TwigNodeTrans.
 *
 * Since twig/twig 3.12 the "tag" argument is deprecated and ignored.
 * Drupal core removed the parameter in issue #3473440.
 *
 * @see https://www.drupal.org/node/3473440
 * @see https://www.drupal.org/node/3474692
 */
final class RemoveTwigNodeTransTagArgumentRector extends AbstractDrupalCoreRector
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
            'Remove the deprecated 6th $tag argument from TwigNodeTrans constructor calls',
            [
                new ConfiguredCodeSample(
                    'new TwigNodeTrans($body, $plural, $count, $options, $lineno, $this->getTag());',
                    'new TwigNodeTrans($body, $plural, $count, $options, $lineno);',
                    [new DrupalIntroducedVersionConfiguration('11.2.0')]
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
        $className = $this->getName($node->class);
        if ($className !== 'TwigNodeTrans'
            && $className !== 'Drupal\Core\Template\TwigNodeTrans'
        ) {
            return null;
        }
        if (count($node->args) !== 6) {
            return null;
        }

        $cloned = clone $node;
        array_pop($cloned->args);

        return $cloned;
    }
}
