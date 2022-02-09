<?php

function file_functions()
{
    file_move();
}
?>
-----
<?php

function file_functions()
{
    \Drupal::service('file.repository')->move();
}
?>
