<?php

namespace Drupal\Tests\rector_fixture {

function openDialog(string $content, array $opts): \Drupal\Core\Ajax\OpenDialogCommand
{
    return new \Drupal\Core\Ajax\OpenDialogCommand('#my-dialog', 'Title', $content, $opts);
}

}
?>
