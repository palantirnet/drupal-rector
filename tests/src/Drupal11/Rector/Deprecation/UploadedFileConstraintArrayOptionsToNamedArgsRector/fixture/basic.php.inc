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
    return \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => new UploadedFileConstraint(maxSize: 1024000, groups: ['myGroup']), fn() => new UploadedFileConstraint(['maxSize' => 1024000, 'groups' => ['myGroup']]));
}

?>
