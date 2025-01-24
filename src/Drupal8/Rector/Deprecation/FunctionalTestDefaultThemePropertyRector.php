<?php

declare(strict_types=1);

namespace DrupalRector\Drupal8\Rector\Deprecation;

use Drupal\Tests\BrowserTestBase;
use PhpParser\Builder\Property;
use PhpParser\Node;
use PHPStan\Type\ObjectType;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\Exception\ShouldNotHappenException;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\PHPStan\ScopeFetcher;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @changelog https://www.drupal.org/node/3083055
 */
final class FunctionalTestDefaultThemePropertyRector extends AbstractRector
{
    /**
     * @var PhpDocInfoFactory
     */
    private PhpDocInfoFactory $phpDocInfoFactory;

    /**
     * @var ValueResolver
     */
    private ValueResolver $valueResolver;

    public function __construct(ValueResolver $valueResolver, PhpDocInfoFactory $phpDocInfoFactory)
    {
        $this->phpDocInfoFactory = $phpDocInfoFactory;
        $this->valueResolver = $valueResolver;
    }

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
            ),
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
     * @param Node\Stmt\Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->isAbstract() || $node->isAnonymous()) {
            return null;
        }
        $type = $this->nodeTypeResolver->getType($node);

        if (count($type->getObjectClassNames()) === 0 || !$type->isObject()->yes()) {
            return null;
        }

        $browserTestBaseType = new ObjectType(BrowserTestBase::class);
        if ($type->isSmallerThanOrEqual($browserTestBaseType)->yes()) {
            return null;
        }
        if ($type->isSuperTypeOf(new ObjectType(BrowserTestBase::class))->no()) {
            return null;
        }
        $classReflection = $type->getClassReflection();
        if ($classReflection === null || !$classReflection->hasNativeProperty('defaultTheme')) {
            throw new ShouldNotHappenException(sprintf('Functional test class %s should have had a defaultTheme property but one not found.', $type->getClassName()));
        }

        $scope = ScopeFetcher::fetch($node);
        $defaultThemeProperty = $classReflection->getProperty('defaultTheme', $scope);
        assert($defaultThemeProperty instanceof \PHPStan\Reflection\Php\PhpPropertyReflection);

        $reflectionProperty = $defaultThemeProperty->getNativeReflection();
        $betterReflection = $reflectionProperty->getBetterReflection();
        $defaultValueExpression = $betterReflection->getDefaultValueExpression();

        if ($defaultValueExpression instanceof Node\Scalar\String_ && strlen($this->valueResolver->getValue($defaultValueExpression)) > 0) {
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
        $node->stmts = array_merge([$property, new Node\Stmt\Nop()], $node->stmts);

        return $node;
    }
}
