<?php

declare(strict_types=1);

namespace DrupalRector\Rector\Class_;

use Drupal\Tests\BrowserTestBase;
use PhpParser\Builder\Property;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Rector\Core\Exception\ShouldNotHappenException;
use Rector\Core\Rector\AbstractScopeAwareRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://www.drupal.org/node/3083055
 *
 * @see \DrupalRector\Tests\Rector\Class_\FunctionalTestDefaultThemePropertyRector\FunctionalTestDefaultThemePropertyRectorTest
 */
final class FunctionalTestDefaultThemePropertyRector extends AbstractScopeAwareRector
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
    public function refactorWithScope(Node $node, Scope $scope): ?Node
    {
        assert($node instanceof Node\Stmt\Class_);
        if ($node->isAbstract() || $node->isAnonymous()) {
            return null;
        }
        $type = $this->nodeTypeResolver->getType($node);
        if (!$type instanceof ObjectType) {
            throw new ShouldNotHappenException(__CLASS__ . ' type for node was not ' . ObjectType::class);
        }
        $browserTestBaseType = new ObjectType(BrowserTestBase::class);
        if ($type->isSmallerThanOrEqual($browserTestBaseType)->yes()) {
            return null;
        }
        if ($type->isSuperTypeOf(new ObjectType(BrowserTestBase::class))->no()) {
            return null;
        }
        $defaultThemeProperty = $type->getProperty('defaultTheme', $scope);
        $nativeProperty = $defaultThemeProperty->getDeclaringClass()->getNativeProperty('defaultTheme');

        // Get the default value for the property. PHPStan's reflection for
        // getting the default value as an expression throws a LogicException
        // when the value is null and not a typed property.
        try {
            $defaultValueExpr = $nativeProperty->getNativeReflection()->getDefaultValueExpr();
            $defaultThemeValue = $this->valueResolver->getValue($defaultValueExpr);
        } catch (\LogicException $e) {
            $defaultThemeValue = null;
        }

        if ($defaultThemeValue !== null) {
            return null;
        }

        // Is processed by \Rector\PostRector\Rector\PropertyAddingPostRector
        // Sets as `private`, which we need `protected` and default value.
        $propertyBuilder = new Property('defaultTheme');
        $propertyBuilder->makeProtected();
        $propertyBuilder->setDefault('stark');
        $propertyBuilder->setDocComment("/**\n * {@inheritdoc}\n */");
        $property = $propertyBuilder->getNode();
        $this->phpDocInfoFactory->createFromNode($property);
        $node->stmts = array_merge([$property], $node->stmts);

        return $node;
    }
}
