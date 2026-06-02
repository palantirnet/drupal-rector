<?php

namespace Drupal\Tests\rector_fixture {

function openModal(string $content): \Drupal\Core\Ajax\OpenModalDialogCommand
{
    return new \Drupal\Core\Ajax\OpenModalDialogCommand('Title', $content, ['dialogClass' => 'modal-class', 'width' => 800]);
}

}
?>
-----
<?php

namespace Drupal\Tests\rector_fixture {

function openModal(string $content): \Drupal\Core\Ajax\OpenModalDialogCommand
{
    return new \Drupal\Core\Ajax\OpenModalDialogCommand('Title', $content, ['width' => 800, 'classes' => ['ui-dialog' => 'modal-class']]);
}

}
?>
