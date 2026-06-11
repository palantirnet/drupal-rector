<?php

namespace Drupal\foo;

use Drupal\file\Validation\Constraint\UploadedFileConstraint;

// Already using named arguments: nothing to do (proves idempotency).
$constraint = new UploadedFileConstraint(maxSize: 1024000);

?>
