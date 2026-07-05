<?php

namespace Drupal\mymodule\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AlreadyTypedValidator extends ConstraintValidator {

    public function validate(mixed $value, Constraint $constraint): void {
    }

}
?>
