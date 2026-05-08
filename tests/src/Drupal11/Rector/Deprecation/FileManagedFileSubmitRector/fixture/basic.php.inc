<?php

function example() {
    $form['upload']['#submit'] = ['file_managed_file_submit'];
    $form['actions']['#submit'][] = 'file_managed_file_submit';
}
?>
-----
<?php

function example() {
    $form['upload']['#submit'] = [[\Drupal\file\Element\ManagedFile::class, 'submit']];
    $form['actions']['#submit'][] = [\Drupal\file\Element\ManagedFile::class, 'submit'];
}
?>
