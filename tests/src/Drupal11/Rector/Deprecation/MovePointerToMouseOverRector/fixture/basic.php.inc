<?php

/** @var \Drupal\Tests\layout_builder\FunctionalJavascript\LayoutBuilderDisableInteractionsTest $test */
$test->movePointerTo('#my-element');
$test->movePointerTo('#another-id');
?>
-----
<?php

/** @var \Drupal\Tests\layout_builder\FunctionalJavascript\LayoutBuilderDisableInteractionsTest $test */
$test->getSession()->getDriver()->mouseOver('.//*[@id="my-element"]');
$test->getSession()->getDriver()->mouseOver('.//*[@id="another-id"]');
?>
