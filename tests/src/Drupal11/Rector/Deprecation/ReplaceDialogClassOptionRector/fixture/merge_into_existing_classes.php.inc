<?php

namespace Drupal\Tests\rector_fixture {

function openDialog(string $content): \Drupal\Core\Ajax\OpenDialogCommand
{
    return new \Drupal\Core\Ajax\OpenDialogCommand('#x', 'T', $content, ['dialogClass' => 'extra', 'classes' => ['ui-dialog-content' => 'pad']]);
}

}
?>
-----
<?php

namespace Drupal\Tests\rector_fixture {

function openDialog(string $content): \Drupal\Core\Ajax\OpenDialogCommand
{
    return new \Drupal\Core\Ajax\OpenDialogCommand('#x', 'T', $content, ['classes' => ['ui-dialog-content' => 'pad', 'ui-dialog' => 'extra']]);
}

}
?>
