<?php

function file_functions()
{
    file_copy();
}
?>
-----
<?php

function file_functions()
{
    \Drupal::service('file.repository')->copy();
}
?>
