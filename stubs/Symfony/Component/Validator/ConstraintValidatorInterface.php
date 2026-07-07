<?php

declare(strict_types=1);

namespace Symfony\Component\Validator;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

if (interface_exists(\Symfony\Component\Validator\ConstraintValidatorInterface::class)) {
    return;
}

interface ConstraintValidatorInterface
{
    public function initialize(ExecutionContextInterface $context);

    public function validate(mixed $value, Constraint $constraint);
}
