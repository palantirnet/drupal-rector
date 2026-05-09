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
$form['wrapper']['group']['settings'] = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => [
    '#type' => 'fieldset',
    '#title' => 'Advanced settings',
    '#weight' => 10,
], fn() => [
    '#type' => 'fieldgroup',
    '#title' => 'Advanced settings',
    '#weight' => 10,
]);

?>
