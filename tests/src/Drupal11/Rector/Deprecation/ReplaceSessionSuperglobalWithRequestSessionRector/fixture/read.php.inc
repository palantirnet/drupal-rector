<?php

$value = $_SESSION['my_key'];
echo $_SESSION['my_key'];
$list = [$_SESSION['a'], 'b' => $_SESSION['b']];
foreach ($_SESSION['items'] as $item) {
    process($item, $_SESSION['context']);
}
if ($_SESSION['flag'] && !$_SESSION['other']) {
    return $_SESSION['result'];
}
$obj = $_SESSION['object']->property;

?>
-----
<?php

$value = \Drupal::request()->getSession()->get('my_key');
echo \Drupal::request()->getSession()->get('my_key');
$list = [\Drupal::request()->getSession()->get('a'), 'b' => \Drupal::request()->getSession()->get('b')];
foreach (\Drupal::request()->getSession()->get('items') as $item) {
    process($item, \Drupal::request()->getSession()->get('context'));
}
if (\Drupal::request()->getSession()->get('flag') && !\Drupal::request()->getSession()->get('other')) {
    return \Drupal::request()->getSession()->get('result');
}
$obj = \Drupal::request()->getSession()->get('object')->property;

?>
