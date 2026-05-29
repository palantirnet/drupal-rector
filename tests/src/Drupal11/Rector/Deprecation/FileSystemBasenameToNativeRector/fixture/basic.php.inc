<?php

/** @var \Drupal\Core\File\FileSystemInterface $fileSystem */
$base = $fileSystem->basename($uri, '.txt');
$noop = $untyped->basename($uri, '.txt');

?>
-----
<?php

/** @var \Drupal\Core\File\FileSystemInterface $fileSystem */
$base = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => basename($uri, '.txt'), fn() => $fileSystem->basename($uri, '.txt'));
$noop = $untyped->basename($uri, '.txt');

?>
