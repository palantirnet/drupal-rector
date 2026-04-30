<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated Error::currentErrorHandler() with get_error_handler().
 *
 * Deprecated in drupal:11.3.0 and removed in drupal:13.0.0.
 *
 * @see https://www.drupal.org/node/3526515
 */
final class ErrorCurrentErrorHandlerRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated \\Drupal\\Core\\Utility\\Error::currentErrorHandler() with PHP built-in get_error_handler()',
            [
                new CodeSample(
                    '$handler = \\Drupal\\Core\\Utility\\Error::currentErrorHandler();',
                    '$handler = get_error_handler();'
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, 'currentErrorHandler')) {
            return null;
        }
        if (!$this->isObjectType($node->class, new ObjectType('Drupal\Core\Utility\Error'))) {
            return null;
        }

        return new FuncCall(new Name('get_error_handler'), []);
    }
}
