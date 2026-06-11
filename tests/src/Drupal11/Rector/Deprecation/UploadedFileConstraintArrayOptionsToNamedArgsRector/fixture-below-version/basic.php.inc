<?php

namespace Drupal\foo;

use Drupal\file\Validation\Constraint\UploadedFileConstraint;

function makeConstraint() {
    return new UploadedFileConstraint(['maxSize' => 1024000, 'groups' => ['myGroup']]);
}

?>
-----
<?php

namespace Drupal\foo;

use Drupal\file\Validation\Constraint\UploadedFileConstraint;

function makeConstraint() {
    return new UploadedFileConstraint(['maxSize' => 1024000, 'groups' => ['myGroup']]);
}

?>
