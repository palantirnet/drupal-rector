<?php

$builder = new \Drupal\comment\CommentLinkBuilder($currentUser, $commentManager, $moduleHandler, $stringTranslation, $entityTypeManager);

?>
-----
<?php

$builder = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => new \Drupal\comment\CommentLinkBuilder($currentUser, $commentManager, $stringTranslation), fn() => new \Drupal\comment\CommentLinkBuilder($currentUser, $commentManager, $moduleHandler, $stringTranslation, $entityTypeManager));

?>
