<?php

/** @var \Drupal\Core\File\FileSystem $fileSystem */
$base = $fileSystem->basename($uri, '.txt');

?>
-----
<?php

/** @var \Drupal\Core\File\FileSystem $fileSystem */
$base = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => basename($uri, '.txt'), fn() => $fileSystem->basename($uri, '.txt'));

?>
