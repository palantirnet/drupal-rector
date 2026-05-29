<?php

namespace Drupal\Tests\rector_fixture {

function openOffCanvas(string $content): \Drupal\Core\Ajax\OpenOffCanvasDialogCommand
{
    return new \Drupal\Core\Ajax\OpenOffCanvasDialogCommand('Title', $content, ['dialogClass' => 'side-panel', 'width' => 400]);
}

}
?>
-----
<?php

namespace Drupal\Tests\rector_fixture {

function openOffCanvas(string $content): \Drupal\Core\Ajax\OpenOffCanvasDialogCommand
{
    return new \Drupal\Core\Ajax\OpenOffCanvasDialogCommand('Title', $content, ['width' => 400, 'classes' => ['ui-dialog' => 'side-panel']]);
}

}
?>
