<?php

/** @var \Drupal\Core\File\FileSystemInterface $fileSystem */
$base = $fileSystem->basename($uri, '.txt');
$noop = $untyped->basename($uri, '.txt');

?>
-----
<?php

/** @var \Drupal\Core\File\FileSystemInterface $fileSystem */
$base = $fileSystem->basename($uri, '.txt');
$noop = $untyped->basename($uri, '.txt');

?>
