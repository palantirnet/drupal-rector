<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
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
final class RemoveTwigNodeTransTagArgumentRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove the deprecated 6th $tag argument from TwigNodeTrans constructor calls',
            [
                new CodeSample(
                    'new TwigNodeTrans($body, $plural, $count, $options, $lineno, $this->getTag());',
                    'new TwigNodeTrans($body, $plural, $count, $options, $lineno);'
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    public function refactor(Node $node): ?Node
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
        array_pop($node->args);

        return $node;
    }
}
