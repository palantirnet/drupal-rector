<?php

declare(strict_types=1);

namespace DrupalRector\Drupal11\Rector\Deprecation;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaces the `@group legacy` PHPDoc annotation with the PHPUnit 10
 * `#[\PHPUnit\Framework\Attributes\IgnoreDeprecations]` attribute.
 *
 * Drupal 11 dropped the `symfony/phpunit-bridge` dependency and adopted
 * PHPUnit 10, whose native attribute supersedes the bridge's docblock
 * annotation. The annotation is preserved by a Drupal shim for BC, but
 * the attribute is the recommended form going forward.
 *
 * @see https://www.drupal.org/node/3417066
 * @see https://www.drupal.org/node/3365413
 */
class GroupLegacyToIgnoreDeprecationsRector extends AbstractRector
{
    // TODO PHPSTAN_MESSAGES GroupLegacyToIgnoreDeprecationsRector: PHPStan
    //   emits no deprecation for `@group legacy`. The deprecation is a
    //   docblock-annotation convention, not a code-level @deprecated symbol,
    //   so static analysis cannot surface it. The rector matches on the
    //   annotation string directly.
    public const PHPSTAN_MESSAGES = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Replace `@group legacy` docblock annotation with the `#[IgnoreDeprecations]` PHP attribute for PHPUnit 10+ compatibility.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
/**
 * Tests deprecated behaviour.
 *
 * @covers ::foo
 * @group legacy
 */
public function testLegacyBehavior(): void {}
CODE_BEFORE,
                    <<<'CODE_AFTER'
/**
 * Tests deprecated behaviour.
 *
 * @covers ::foo
 */
#[\PHPUnit\Framework\Attributes\IgnoreDeprecations]
public function testLegacyBehavior(): void {}
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Class_::class];
    }

    /** @param ClassMethod|Class_ $node */
    public function refactor(Node $node): ?Node
    {
        $docComment = $node->getDocComment();
        if ($docComment === null) {
            return null;
        }

        $docText = $docComment->getText();
        // Cheap pre-filter first, then a boundary-aware check that mirrors the
        // removal regex below. Without the boundary, a group such as
        // `@group legacy-kernel` would pass the gate and get an unwanted
        // `#[IgnoreDeprecations]` appended while the regex left its annotation
        // line untouched.
        if (!str_contains($docText, '@group legacy')
            || preg_match('/^[ \t]*\*[ \t]*@group legacy[ \t]*\r?$/m', $docText) !== 1
        ) {
            return null;
        }

        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $name = $this->getName($attr->name);
                if ($name === 'PHPUnit\\Framework\\Attributes\\IgnoreDeprecations'
                    || $name === 'IgnoreDeprecations'
                ) {
                    return null;
                }
            }
        }

        $node->attrGroups[] = new AttributeGroup([
            new Attribute(new FullyQualified('PHPUnit\\Framework\\Attributes\\IgnoreDeprecations')),
        ]);

        $newDocText = preg_replace('/^[ \t]*\*[ \t]*@group legacy[ \t]*\r?\n/m', '', $docText);
        $newDocText = preg_replace('/\n[ \t]*\*[ \t]*\n([ \t]*\*\/)$/', "\n$1", $newDocText);

        if (preg_match('/^\/\*\*\s*\*\/\s*$/', trim($newDocText))) {
            $node->setAttribute('comments', []);
        } else {
            $node->setAttribute('comments', [new Doc($newDocText)]);
        }

        return $node;
    }
}
