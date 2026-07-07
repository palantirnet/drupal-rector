<?php

declare(strict_types=1);

namespace Symfony\Component\Validator;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

if (class_exists(\Symfony\Component\Validator\ConstraintValidator::class)) {
    return;
}

abstract class ConstraintValidator implements ConstraintValidatorInterface
{
    public function initialize(ExecutionContextInterface $context): void
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
    }
}
