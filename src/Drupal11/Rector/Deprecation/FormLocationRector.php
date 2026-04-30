<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class FormLocationRector extends AbstractRector
{
    /**
     * Maps deprecated CommentItemInterface constants to FormLocation enum cases.
     */
    private const MAP = [
        'FORM_BELOW' => 'Below',
        'FORM_SEPARATE_PAGE' => 'SeparatePage',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated CommentItemInterface::FORM_BELOW and FORM_SEPARATE_PAGE constants with FormLocation enum cases.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;

$location = CommentItemInterface::FORM_BELOW;
$other = CommentItemInterface::FORM_SEPARATE_PAGE;
CODE_BEFORE,
                    <<<'CODE_AFTER'
$location = \Drupal\comment\FormLocation::Below;
$other = \Drupal\comment\FormLocation::SeparatePage;
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [ClassConstFetch::class];
    }

    /** @param ClassConstFetch $node */
    public function refactor(Node $node): ?Node
    {
        if (!$node->name instanceof Identifier) {
            return null;
        }

        $constName = $node->name->toString();
        if (!array_key_exists($constName, self::MAP)) {
            return null;
        }

        if (!$this->isName($node->class, 'Drupal\\comment\\Plugin\\Field\\FieldType\\CommentItemInterface')) {
            return null;
        }

        $enumCase = self::MAP[$constName];

        return new ClassConstFetch(
            new FullyQualified('Drupal\\comment\\FormLocation'),
            new Identifier($enumCase)
        );
    }
}
