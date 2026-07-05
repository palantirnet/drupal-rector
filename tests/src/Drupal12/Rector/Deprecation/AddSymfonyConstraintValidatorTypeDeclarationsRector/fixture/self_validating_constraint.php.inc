<?php

namespace Drupal\mymodule\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SourceLinkTypeConstraint extends Constraint implements ConstraintValidatorInterface {

    public function initialize(ExecutionContextInterface $context) {
    }

    public function validate($value, Constraint $constraint) {
    }

}
?>
-----
<?php

namespace Drupal\mymodule\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SourceLinkTypeConstraint extends Constraint implements ConstraintValidatorInterface {

    public function initialize(ExecutionContextInterface $context): void {
    }

    public function validate(mixed $value, Constraint $constraint): void {
    }

}
?>
