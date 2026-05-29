<?php

namespace Drupal\Tests\rector_fixture {

function openDialog(string $content): \Drupal\Core\Ajax\OpenDialogCommand
{
    return new \Drupal\Core\Ajax\OpenDialogCommand('#my-dialog', 'Title', $content, ['dialogClass' => 'my-class', 'width' => 600]);
}

}
?>
-----
<?php

namespace Drupal\Tests\rector_fixture {

function openDialog(string $content): \Drupal\Core\Ajax\OpenDialogCommand
{
    return new \Drupal\Core\Ajax\OpenDialogCommand('#my-dialog', 'Title', $content, ['width' => 600, 'classes' => ['ui-dialog' => 'my-class']]);
}

}
?>
