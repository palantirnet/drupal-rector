<?php

namespace Drupal\mymodule\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueHashValidator extends ConstraintValidator {

    public function validate($redirect, Constraint $constraint) {
    }

}
?>
-----
<?php

namespace Drupal\mymodule\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueHashValidator extends ConstraintValidator {

    public function validate(mixed $redirect, Constraint $constraint): void {
    }

}
?>
