<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces deprecated DrupalTestCaseTrait::getDrupalRoot() instance calls with $this->root property access.
 *
 * The method was deprecated in drupal:11.4.0 and removed in drupal:13.0.0. The rule targets
 * subclasses of BrowserTestBase, KernelTestBase, and UnitTestCase — all of which expose the
 * pre-existing $root property via DrupalTestCaseTrait. Static calls (static::getDrupalRoot())
 * are intentionally left untouched: callers in @dataProvider methods cannot reach $this->root
 * and require a structural rewrite. BuildTestBase overrides getDrupalRoot() with a non-deprecated
 * implementation and is also left untouched.
 *
 * @see https://www.drupal.org/node/3589047
 * @see https://www.drupal.org/node/3574112
 */
final class GetDrupalRootToRootPropertyRector extends AbstractRector
{
    public const PHPSTAN_MESSAGES = [
        'Call to deprecated method getDrupalRoot() of class Drupal\Tests\BrowserTestBase. Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Access $this->root directly.',
        'Call to deprecated method getDrupalRoot() of class Drupal\KernelTests\KernelTestBase. Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Access $this->root directly.',
        'Call to deprecated method getDrupalRoot() of class Drupal\Tests\UnitTestCase. Deprecated in drupal:11.4.0 and is removed from drupal:13.0.0. Access $this->root directly.',
    ];

    /**
     * Base test classes that use DrupalTestCaseTrait and do not override getDrupalRoot().
     *
     * @var array<string>
     */
    private const BASE_TEST_CLASSES = [
        'Drupal\Tests\BrowserTestBase',
        'Drupal\KernelTests\KernelTestBase',
        'Drupal\Tests\UnitTestCase',
    ];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace deprecated DrupalTestCaseTrait::getDrupalRoot() calls with $this->root property access in Drupal test classes.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
$dir = $this->getDrupalRoot() . '/core/tests/fixtures';
CODE_BEFORE,
                    <<<'CODE_AFTER'
$dir = $this->root . '/core/tests/fixtures';
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
        if (!$this->isName($node->name, 'getDrupalRoot')) {
            return null;
        }

        if (count($node->args) !== 0) {
            return null;
        }

        foreach (self::BASE_TEST_CLASSES as $fqcn) {
            if ($this->isObjectType($node->var, new ObjectType($fqcn))) {
                return new PropertyFetch($node->var, new Identifier('root'));
            }
        }

        return null;
    }
}
