<?php

declare(strict_types=1);

namespace DrupalRector\Drupal12\Rector\Deprecation;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Adds the Symfony 8 / Drupal 12 type declarations to
 * ConstraintValidatorInterface implementers.
 *
 * Symfony 7.0 (Drupal 11.0) added `mixed $value` to
 * ConstraintValidatorInterface::validate(); Symfony 8.0 (Drupal 12.0) added
 * native `: void` return types to validate() and initialize(). This rector adds
 * those to any implementer (`extends ConstraintValidator`,
 * `implements ConstraintValidatorInterface`, or the legacy
 * `extends Constraint implements ConstraintValidatorInterface`).
 *
 * The change is backward compatible on every supported Drupal version: a child
 * may declare types its interface does not (Drupal core itself ships
 * `validate(mixed $value, Constraint $constraint): void` while running on a
 * Symfony 7.4 interface without the native `: void`), so no BC wrapping or
 * version gate is needed.
 *
 * @see https://git.drupalcode.org/project/redirect/-/merge_requests/200 (issue #3602388)
 * @see https://github.com/symfony/symfony/blob/8.0/src/Symfony/Component/Validator/ConstraintValidatorInterface.php
 */
final class AddSymfonyConstraintValidatorTypeDeclarationsRector extends AbstractRector
{
    private const CONSTRAINT_VALIDATOR_INTERFACE = 'Symfony\Component\Validator\ConstraintValidatorInterface';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Add Symfony ConstraintValidatorInterface type declarations (mixed $value / : void) to validator classes.',
            [
                new CodeSample(
                    <<<'CODE_BEFORE'
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MyValidator extends ConstraintValidator {
    public function validate($value, Constraint $constraint) {
    }
}
CODE_BEFORE,
                    <<<'CODE_AFTER'
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MyValidator extends ConstraintValidator {
    public function validate(mixed $value, Constraint $constraint): void {
    }
}
CODE_AFTER
                ),
            ]
        );
    }

    /** @return array<class-string<Node>> */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if (!$this->isObjectType($node, new ObjectType(self::CONSTRAINT_VALIDATOR_INTERFACE))) {
            return null;
        }

        $changed = false;

        $validate = $node->getMethod('validate');
        if ($validate instanceof ClassMethod) {
            if ($this->addMixedFirstParam($validate)) {
                $changed = true;
            }
            if ($this->addVoidReturnType($validate)) {
                $changed = true;
            }
        }

        $initialize = $node->getMethod('initialize');
        if ($initialize instanceof ClassMethod) {
            if ($this->addVoidReturnType($initialize)) {
                $changed = true;
            }
        }

        return $changed ? $node : null;
    }

    private function addVoidReturnType(ClassMethod $method): bool
    {
        if ($method->returnType !== null) {
            return false;
        }

        $method->returnType = new Identifier('void');

        return true;
    }

    private function addMixedFirstParam(ClassMethod $method): bool
    {
        if (!isset($method->params[0]) || $method->params[0]->type !== null) {
            return false;
        }

        $method->params[0]->type = new Identifier('mixed');

        return true;
    }
}
