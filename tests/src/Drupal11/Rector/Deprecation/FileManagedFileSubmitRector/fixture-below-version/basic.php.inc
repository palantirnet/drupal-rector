<?php

function example() {
    $form['upload']['#submit'] = ['file_managed_file_submit'];
    $form['actions']['#submit'][] = 'file_managed_file_submit';
}
?>
-----
<?php

function example() {
    $form['upload']['#submit'] = ['file_managed_file_submit'];
    $form['actions']['#submit'][] = 'file_managed_file_submit';
}
?>
