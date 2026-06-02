<?php

namespace Drupal\Tests\rector_fixture {

// Pathological duplicate `dialogClass` keys: PHP de-duplicates at array
// construction (last wins) but the AST keeps both. The rector bails so it
// never partially rewrites — picking one would leave the other in place,
// keeping the deprecated key alive.
function withDuplicateDialogClass() {
    return new \Drupal\Core\Ajax\OpenDialogCommand(
        '#my-dialog',
        'Title',
        'content',
        [
            'dialogClass' => 'first',
            'dialogClass' => 'second',
        ]
    );
}

}
?>
