<?php

// Unrelated class — type guard prevents transformation.
/** @var \SomeOtherClass $obj */
$obj->setConfigurationValue('items_per_page', 'none');

// Different first arg — must not change.
/** @var \Drupal\views\Plugin\Block\ViewsBlockBase $block */
$block->setConfigurationValue('label', 'none');

// Different second arg value — must not change.
$block->setConfigurationValue('items_per_page', 5);

// Different method name on the same type — must not change.
$block->setConfiguration(['items_per_page' => 'none']);
?>
-----
<?php

// Unrelated class — type guard prevents transformation.
/** @var \SomeOtherClass $obj */
$obj->setConfigurationValue('items_per_page', 'none');

// Different first arg — must not change.
/** @var \Drupal\views\Plugin\Block\ViewsBlockBase $block */
$block->setConfigurationValue('label', 'none');

// Different second arg value — must not change.
$block->setConfigurationValue('items_per_page', 5);

// Different method name on the same type — must not change.
$block->setConfiguration(['items_per_page' => 'none']);
?>
