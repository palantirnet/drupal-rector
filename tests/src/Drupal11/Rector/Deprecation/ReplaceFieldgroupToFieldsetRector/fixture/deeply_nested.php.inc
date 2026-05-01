<?php

// '#type' => 'fieldgroup' inside a deeply nested form array must be changed.
$form['wrapper']['group']['settings'] = [
    '#type' => 'fieldgroup',
    '#title' => 'Advanced settings',
    '#weight' => 10,
];

?>
-----
<?php

// '#type' => 'fieldgroup' inside a deeply nested form array must be changed.
$form['wrapper']['group']['settings'] = [
    '#type' => 'fieldset',
    '#title' => 'Advanced settings',
    '#weight' => 10,
];

?>
