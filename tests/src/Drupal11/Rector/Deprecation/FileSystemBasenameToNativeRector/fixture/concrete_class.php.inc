<?php

/** @var \Drupal\Core\File\FileSystem $fileSystem */
$base = $fileSystem->basename($uri, '.txt');

?>
-----
<?php

/** @var \Drupal\Core\File\FileSystem $fileSystem */
$base = basename($uri, '.txt');

?>
