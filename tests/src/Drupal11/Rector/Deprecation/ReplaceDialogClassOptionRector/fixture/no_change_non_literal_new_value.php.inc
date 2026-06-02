<?php

namespace Drupal\Tests\rector_fixture {

// Concatenation path requires BOTH the existing 'ui-dialog' value and the
// new dialogClass value to be string literals. When the new value is a
// variable, the rector must leave the array untouched — partially deleting
// dialogClass without inserting a replacement would silently lose the
// option.
function withNonLiteralDialogClass(string $cssClass) {
    return new \Drupal\Core\Ajax\OpenDialogCommand(
        '#my-dialog',
        'Title',
        'content',
        [
            'dialogClass' => $cssClass,
            'classes' => ['ui-dialog' => 'base'],
        ]
    );
}

}
?>
