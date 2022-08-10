<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Class_;

use Drupal\Tests\BrowserTestBase;
use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\Rector\AbstractRector;
use Symplify\Astral\ValueObject\NodeBuilder\PropertyBuilder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://www.drupal.org/node/3083055
 *
 * @see \DrupalRector\Tests\Rector\Class_\FunctionalTestDefaultThemePropertyRector\FunctionalTestDefaultThemePropertyRectorTest
 */
final class FunctionalTestDefaultThemePropertyRector extends AbstractRector
{

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Adds $defaultTheme property to Functional and FunctionalJavascript tests which do not have them.', [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClassTest {
}
CODE_SAMPLE

                ,
                <<<'CODE_SAMPLE'
class SomeClassTest {
  protected $defaultTheme = 'stark'
}
CODE_SAMPLE

            )
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Node\Stmt\Class_::class];
    }

    /**
     * @param \PhpParser\Node\Stmt\Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        assert($node instanceof Node\Stmt\Class_);
        if ($node->isAbstract() || $node->isAnonymous()) {
            return null;
        }
        $type = $this->nodeTypeResolver->getType($node);
        if (!$type instanceof ObjectType) {
            throw new ShouldNotHappenException(__CLASS__ . ' type for node was not ' . ObjectType::class);
        }
        if ($type->isSuperTypeOf(new ObjectType(BrowserTestBase::class))->no()) {
            return null;
        }
        if ($type->hasProperty('defaultTheme')->yes()) {
            // @todo check value. if `classy` change to `starterkit`?
            return null;
        }
        // Is processed by \Rector\PostRector\Rector\PropertyAddingPostRector
        // Sets as `private`, which we need `protected` and default value.
        $propertyBuilder = new PropertyBuilder('defaultTheme');
        $propertyBuilder->makeProtected();
        $propertyBuilder->setDefault('stark');
        $node->stmts = array_merge([$propertyBuilder->getNode()], $node->stmts);

        return $node;
    }
}
