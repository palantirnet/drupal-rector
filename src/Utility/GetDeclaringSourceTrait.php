<?php declare(strict_types=1);

namespace DrupalRector\Utility;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Php\PhpMethodReflection;
use Rector\NodeTypeResolver\Node\AttributeKey;

trait GetDeclaringSourceTrait
{

    /**
     * Gets a method or property's declaring source (trait or class.)
     *
     * @param Node\Expr\MethodCall|Node\Expr\PropertyFetch $expr
     *   The expression.
     *
     * @return string|null
     *   The declaring source (trait or class.)
     */
    protected function getDeclaringSource(Node\Expr $expr): ?string
    {
        $scope = $expr->getAttribute(AttributeKey::SCOPE);
        if (!$scope instanceof Scope) {
            return null;
        }
        $classReflection = $scope->getClassReflection();
        assert($classReflection !== null);

        $name = $this->getName($expr->name);
        if ($expr instanceof Node\Expr\MethodCall) {
            $exprReflection = $classReflection->getMethod($name, $scope);
            // Concrete method has getDeclaringTrait, not interface.
            if (!$exprReflection instanceof PhpMethodReflection) {
                return null;
            }
        } elseif ($expr instanceof Node\Expr\PropertyFetch) {
            $exprReflection = $classReflection->getProperty($name, $scope);
        } else {
            throw new \InvalidArgumentException(
                "Can only call getDeclaringSource on MethodCall or PropertyFetch. Received: " . get_class($expr)
            );
        }

        $declaringTrait = $exprReflection->getDeclaringTrait();
        if ($declaringTrait !== null) {
            return $declaringTrait->getName();
        }
        $declaringClass = $exprReflection->getDeclaringClass();
        if ($declaringClass !== null) {
            return $declaringClass->getName();
        }
        return null;
    }

}
