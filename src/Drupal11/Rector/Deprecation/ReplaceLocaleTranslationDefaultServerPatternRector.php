<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name\FullyQualified;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN constant with \Drupal::TRANSLATION_DEFAULT_SERVER_PATTERN.
 *
 * Deprecated in drupal:11.2.0, removed in drupal:12.0.0.
 *
 * @see https://www.drupal.org/node/3477277
 */
final class ReplaceLocaleTranslationDefaultServerPatternRector extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [ConstFetch::class];
    }

    public function refactor(Node $node): mixed
    {
        assert($node instanceof ConstFetch);

        if (!$this->isName($node, 'LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN')) {
            return null;
        }

        return new ClassConstFetch(
            new FullyQualified('Drupal'),
            'TRANSLATION_DEFAULT_SERVER_PATTERN'
        );
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace deprecated LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN with \\Drupal::TRANSLATION_DEFAULT_SERVER_PATTERN (drupal:11.2.0)', [
            new CodeSample(
                '$pattern = LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN;',
                '$pattern = \\Drupal::TRANSLATION_DEFAULT_SERVER_PATTERN;'
            ),
        ]);
    }
}
