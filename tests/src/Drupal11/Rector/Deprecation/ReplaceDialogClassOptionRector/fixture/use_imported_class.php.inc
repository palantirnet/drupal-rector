<?php

namespace Drupal\Tests\rector_fixture {

use Drupal\Core\Ajax\OpenDialogCommand;

function openDialog(string $content): OpenDialogCommand
{
    return new OpenDialogCommand('#my-dialog', 'Title', $content, ['dialogClass' => 'imported-class']);
}

}
?>
-----
<?php

namespace Drupal\Tests\rector_fixture {

use Drupal\Core\Ajax\OpenDialogCommand;

function openDialog(string $content): OpenDialogCommand
{
    return new OpenDialogCommand('#my-dialog', 'Title', $content, ['classes' => ['ui-dialog' => 'imported-class']]);
}

}
?>
