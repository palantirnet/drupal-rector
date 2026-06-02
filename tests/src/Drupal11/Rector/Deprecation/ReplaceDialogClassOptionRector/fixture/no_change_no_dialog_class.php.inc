<?php

namespace Drupal\Tests\rector_fixture {

function openDialog(string $content): \Drupal\Core\Ajax\OpenDialogCommand
{
    return new \Drupal\Core\Ajax\OpenDialogCommand('#my-dialog', 'Title', $content, ['width' => 600, 'classes' => ['ui-dialog' => 'already-correct']]);
}

}
?>
