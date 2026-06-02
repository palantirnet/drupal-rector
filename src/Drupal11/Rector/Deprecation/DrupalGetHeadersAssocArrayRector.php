<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Rewrites deprecated drupalGet() $headers argument formats:
 *  - Integer-keyed 'Header-Name: value' strings => ['Header-Name' => 'value']
 *  - Null header values => empty string ''.
 *
 * Deprecated in drupal:11.1.0 and removed in drupal:12.0.0. The replacement is
 * the documented associative format, which has always been valid; no new
 * Drupal API is involved, so no BC wrapping is needed.
 *
 * @see https://www.drupal.org/node/3440169
 * @see https://www.drupal.org/node/3456178
 * @see https://www.drupal.org/node/3456233
 */
class DrupalGetHeadersAssocArrayRector extends AbstractRector
{
    // TODO PHPSTAN_MESSAGES DrupalGetHeadersAssocArrayRector: PHPStan emits no
    //   deprecation for the targeted call. The deprecation is triggered at
    //   runtime via @trigger_error inside UiHelperTrait::drupalGet() when a
    //   header name is an integer or a header value is null. The method itself
    //   carries no @deprecated annotation, so phpstan-deprecation-rules does
    //   not flag callers. No string is available to add here.

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert drupalGet() $headers from indexed colon-separated strings or null values to an associative array, as required by Drupal 11.1.0.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
$this->drupalGet('/path', [], ['X-Requested-With: XMLHttpRequest']);
$this->drupalGet('', [], ['Accept-Language' => NULL]);
CODE_BEFORE,
                    <<<'CODE_AFTER'
$this->drupalGet('/path', [], ['X-Requested-With' => 'XMLHttpRequest']);
$this->drupalGet('', [], ['Accept-Language' => '']);
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

    /**
     * @param MethodCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isName($node->name, 'drupalGet')) {
            return null;
        }

        // The deprecation fires only from Drupal\Tests\UiHelperTrait::drupalGet(),
        // which is used by BrowserTestBase (and its subclasses, including
        // WebDriverTestBase). KernelTestBase uses HttpKernelUiHelperTrait, whose
        // drupalGet() does not emit this deprecation, so we exclude it by typing.
        if (!$this->isObjectType($node->var, new ObjectType('Drupal\Tests\BrowserTestBase'))) {
            return null;
        }

        // $headers is the third argument (index 2).
        if (count($node->args) < 3) {
            return null;
        }

        $headersArg = $node->args[2];
        if (!$headersArg instanceof Arg) {
            return null;
        }

        $headersArray = $headersArg->value;
        if (!$headersArray instanceof Array_) {
            return null;
        }

        $changed = false;

        foreach ($headersArray->items as $item) {
            // Pattern 1: integer-keyed item with 'Header-Name: value' string.
            // Deprecated since drupal:11.1.0 — see https://www.drupal.org/node/3456178
            if ($item->key === null && $item->value instanceof String_) {
                $raw = $item->value->value;
                // Only rewrite when the string uses the conventional
                // `Name: value` form (colon followed by a space). This avoids
                // mis-splitting incidental colon-containing strings like
                // `'http://example.com'` or `'/path?x=y'`. The name part is
                // additionally validated against a conservative ASCII
                // header-name pattern.
                if (str_contains($raw, ': ')) {
                    [$headerName, $headerValue] = explode(':', $raw, 2);
                    $trimmedName = trim($headerName);
                    if (preg_match('/^[A-Za-z][A-Za-z0-9-]*$/', $trimmedName) === 1) {
                        $item->key = new String_($trimmedName);
                        $item->value = new String_(trim($headerValue));
                        $changed = true;
                        continue;
                    }
                }
            }

            // Pattern 2: null header value.
            // Deprecated since drupal:11.1.0 — see https://www.drupal.org/node/3456233
            if ($item->value instanceof ConstFetch
                && strtolower((string) $item->value->name) === 'null'
            ) {
                $item->value = new String_('');
                $changed = true;
            }
        }

        return $changed ? $node : null;
    }
}
