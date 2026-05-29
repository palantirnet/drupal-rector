<?php

$form['account'] = [
    '#type' => 'fieldgroup',
    '#title' => 'Account settings',
];
$form['other'] = [
    '#type' => 'fieldset',
    '#title' => 'Other settings',
];

?>
-----
<?php

$form['account'] = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => [
    '#type' => 'fieldset',
    '#title' => 'Account settings',
], fn() => [
    '#type' => 'fieldgroup',
    '#title' => 'Account settings',
]);
$form['other'] = [
    '#type' => 'fieldset',
    '#title' => 'Other settings',
];

?>
