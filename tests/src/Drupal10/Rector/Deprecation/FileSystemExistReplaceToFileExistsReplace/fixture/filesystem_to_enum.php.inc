<?php

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\File\FileExists;

function simple_example() {
    $something = FileSystemInterface::EXISTS_REPLACE;
}

class AnotherExample {
    public function test() {
        $mode = FileSystemInterface::EXISTS_REPLACE;
    }

    public function foo() {
        str_contains('mystring', FileSystemInterface::EXISTS_REPLACE);
    }
}

?>
-----
<?php

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\File\FileExists;

function simple_example() {
    $something = \Drupal\Core\File\FileExists::Replace;
}

class AnotherExample {
    public function test() {
        $mode = \Drupal\Core\File\FileExists::Replace;
    }

    public function foo() {
        str_contains('mystring', \Drupal\Core\File\FileExists::Replace);
    }
}

?>
