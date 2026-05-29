<?php

use Drupal\Core\File\FileSystemInterface;

function example($fileSystem, $src, $dst, $data) {
    $fileSystem->copy($src, $dst, FileSystemInterface::EXISTS_RENAME);
    $fileSystem->move($src, $dst, FileSystemInterface::EXISTS_REPLACE);
    $fileSystem->saveData($data, $dst, FileSystemInterface::EXISTS_ERROR);
}
?>
-----
<?php

use Drupal\Core\File\FileSystemInterface;

function example($fileSystem, $src, $dst, $data) {
    $fileSystem->copy($src, $dst, \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3.0', fn() => \Drupal\Core\File\FileExists::Rename, fn() => FileSystemInterface::EXISTS_RENAME));
    $fileSystem->move($src, $dst, \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3.0', fn() => \Drupal\Core\File\FileExists::Replace, fn() => FileSystemInterface::EXISTS_REPLACE));
    $fileSystem->saveData($data, $dst, \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.3.0', fn() => \Drupal\Core\File\FileExists::Error, fn() => FileSystemInterface::EXISTS_ERROR));
}
?>
