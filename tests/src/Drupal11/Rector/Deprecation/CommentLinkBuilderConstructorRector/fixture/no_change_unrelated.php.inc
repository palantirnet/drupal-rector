<?php

// Unrelated class with the same 5-argument shape — must not be transformed.
$builder = new \Drupal\other\SomeOtherBuilder($currentUser, $commentManager, $moduleHandler, $stringTranslation, $entityTypeManager);

?>
