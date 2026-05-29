<?php

// Unrelated class — type guard prevents transformation.
/** @var \SomeOtherClass $obj */
$obj->movePointerTo('#my-element');

// Non-ID CSS selector — cannot be auto-converted to XPath.
/** @var \Drupal\Tests\layout_builder\FunctionalJavascript\LayoutBuilderDisableInteractionsTest $test */
$test->movePointerTo('.some-class');

// Dynamic/variable argument — cannot be statically converted.
/** @var \Drupal\Tests\layout_builder\FunctionalJavascript\LayoutBuilderDisableInteractionsTest $test */
$test->movePointerTo($selector);
?>
-----
<?php

// Unrelated class — type guard prevents transformation.
/** @var \SomeOtherClass $obj */
$obj->movePointerTo('#my-element');

// Non-ID CSS selector — cannot be auto-converted to XPath.
/** @var \Drupal\Tests\layout_builder\FunctionalJavascript\LayoutBuilderDisableInteractionsTest $test */
$test->movePointerTo('.some-class');

// Dynamic/variable argument — cannot be statically converted.
/** @var \Drupal\Tests\layout_builder\FunctionalJavascript\LayoutBuilderDisableInteractionsTest $test */
$test->movePointerTo($selector);
?>
