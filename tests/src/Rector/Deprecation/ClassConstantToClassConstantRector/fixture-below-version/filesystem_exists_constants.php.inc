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
    $fileSystem->copy($src, $dst, FileSystemInterface::EXISTS_RENAME);
    $fileSystem->move($src, $dst, FileSystemInterface::EXISTS_REPLACE);
    $fileSystem->saveData($data, $dst, FileSystemInterface::EXISTS_ERROR);
}
?>
