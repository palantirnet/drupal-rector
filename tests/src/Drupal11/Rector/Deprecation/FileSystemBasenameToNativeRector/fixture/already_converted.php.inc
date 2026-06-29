<?php

// Code that was previously converted while the rector still emitted a
// DeprecationHelper wrapper must be left untouched: the deprecated call lives in
// the `deprecatedCallable` arm and is skipped by isInBackwardsCompatibleCall().
/** @var \Drupal\Core\File\FileSystemInterface $fileSystem */
$base = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => basename($uri, '.txt'), fn() => $fileSystem->basename($uri, '.txt'));

?>
