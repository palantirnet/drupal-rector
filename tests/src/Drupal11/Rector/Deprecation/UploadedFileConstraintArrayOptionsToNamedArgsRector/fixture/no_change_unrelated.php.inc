<?php

namespace Drupal\foo;

// An unrelated class that happens to take an options array must not be touched.
$other = new \Drupal\foo\SomeOtherConstraint(['maxSize' => 1024000]);

?>
