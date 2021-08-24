<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Php\PhpMethodReflection;
use Rector\Core\Rector\AbstractRector;
use Rector\NodeCollector\ScopeResolver\ParentClassScopeResolver;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class PassRector extends AbstractRector
{

    /**
     * @var ParentClassScopeResolver
     */
    protected $parentClassScopeResolver;

    public function __construct(ParentClassScopeResolver $parentClassScopeResolver)
    {
        $this->parentClassScopeResolver = $parentClassScopeResolver;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::pass() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->pass('The whole transaction is rolled back when a duplicate key insert occurs.');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
CODE_AFTER
            )
        ]);
    }

    public function getNodeTypes(): array
    {
        return [
            Node\Expr\MethodCall::class,
        ];
    }

    public function refactor(Node $node)
    {
        assert($node instanceof Node\Expr\MethodCall);
        if ($this->getName($node->name) !== 'pass') {
            return null;
        }

        // @todo Maybe make this a service? Definitely needed in AssertLegacyTraitBase.
        $scope = $node->getAttribute(AttributeKey::SCOPE);
        assert($scope instanceof Scope);
        $classReflection = $scope->getClassReflection();
        assert($classReflection !== null);
        $passReflection = $classReflection->getMethod('pass', $scope);
        if (!$passReflection instanceof PhpMethodReflection) {
            return null;
        }
        $declaringTrait = $passReflection->getDeclaringTrait();
        if ($declaringTrait === null) {
            return null;
        }
        if ($declaringTrait->getName() === 'Drupal\KernelTests\AssertLegacyTrait') {
            $this->removeNode($node);
        }

        return $node;
    }
}
