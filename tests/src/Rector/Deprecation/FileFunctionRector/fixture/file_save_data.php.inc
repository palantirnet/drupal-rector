<?php

function file_functions()
{
    file_save_data();
}
?>
-----
<?php

function file_functions()
{
    \Drupal::service('file.repository')->writeData();
}
?>
