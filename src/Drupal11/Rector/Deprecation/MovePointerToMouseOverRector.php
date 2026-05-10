<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated movePointerTo() with getSession()->getDriver()->mouseOver().
 *
 * Deprecated in drupal:11.1.0 and removed in drupal:12.0.0. The replacement
 * requires an XPath selector instead of a CSS selector. This rule handles
 * simple CSS ID selectors (#foo), converting them to .//*[@id="foo"].
 *
 * @see https://www.drupal.org/node/3421202
 * @see https://www.drupal.org/node/3460567
 */
class MovePointerToMouseOverRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated movePointerTo() with getSession()->getDriver()->mouseOver()',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
$this->movePointerTo('#my-element');
CODE_BEFORE,
                    <<<'CODE_AFTER'
$this->getSession()->getDriver()->mouseOver('.//*[@id="my-element"]');
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /** @param MethodCall $node */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, 'movePointerTo')) {
            return null;
        }

        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Tests\layout_builder\FunctionalJavascript\LayoutBuilderDisableInteractionsTest'))) {
            return null;
        }

        if (count($node->args) !== 1) {
            return null;
        }

        $arg = $node->args[0];
        if (!$arg instanceof Arg) {
            return null;
        }

        $argValue = $arg->value;
        if (!$argValue instanceof String_) {
            return null;
        }

        $cssSelector = $argValue->value;

        if (!preg_match('/^#([a-zA-Z][a-zA-Z0-9_-]*)$/', $cssSelector, $matches)) {
            return null;
        }

        $xpathSelector = './/*[@id="'.$matches[1].'"]';

        $getSession = new MethodCall($node->var, 'getSession', []);
        $getDriver = new MethodCall($getSession, 'getDriver', []);

        return new MethodCall(
            $getDriver,
            'mouseOver',
            [new Arg(new String_($xpathSelector))]
        );
    }
}
