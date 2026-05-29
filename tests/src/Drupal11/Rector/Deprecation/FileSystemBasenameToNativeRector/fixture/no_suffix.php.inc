<?php

/** @var \Drupal\Core\File\FileSystemInterface $fileSystem */
$base = $fileSystem->basename($uri);

?>
-----
<?php

/** @var \Drupal\Core\File\FileSystemInterface $fileSystem */
$base = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => basename($uri), fn() => $fileSystem->basename($uri));

?>
