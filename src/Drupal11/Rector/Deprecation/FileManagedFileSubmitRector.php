<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the deprecated 'file_managed_file_submit' string callback with
 * [\Drupal\file\Element\ManagedFile::class, 'submit'] array callable.
 *
 * @see https://www.drupal.org/node/3534089
 * @see https://www.drupal.org/node/3534091
 */
class FileManagedFileSubmitRector extends AbstractRector
{
    private const DEPRECATED_FUNCTION = 'file_managed_file_submit';
    private const NEW_CLASS = 'Drupal\\file\\Element\\ManagedFile';
    private const NEW_METHOD = 'submit';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            "Replace deprecated 'file_managed_file_submit' string callback with [\\Drupal\\file\\Element\\ManagedFile::class, 'submit'] array callable",
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
$form['upload']['#submit'] = ['file_managed_file_submit'];
CODE_BEFORE,
                    <<<'CODE_AFTER'
$form['upload']['#submit'] = [[\Drupal\file\Element\ManagedFile::class, 'submit']];
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [String_::class];
    }

    /** @param String_ $node */
    public function refactor(Node $node): ?Node
    {
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
