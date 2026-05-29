<?php

namespace Drupal\Tests\rector_fixture {

// Merging into an existing `classes` entry requires it to resolve to a
// literal array node. When `classes` is a variable (or any non-Array_
// expression), the rector cannot safely insert into it and must leave
// the array untouched.
function withVariableClasses(array $existingClasses) {
    return new \Drupal\Core\Ajax\OpenDialogCommand(
        '#my-dialog',
        'Title',
        'content',
        [
            'dialogClass' => 'extra-class',
            'classes' => $existingClasses,
        ]
    );
}

}
?>
