<?php

// Unrelated class — type guard prevents transformation.
/** @var \SomeOtherClass $obj */
$dir = $obj->getDrupalRoot();

// BuildTestBase overrides getDrupalRoot() with a non-deprecated implementation; must not change.
/** @var \Drupal\BuildTests\Framework\BuildTestBase $build */
$dir = $build->getDrupalRoot();

// Method called with arguments — must not change (deprecated method is parameterless).
/** @var \Drupal\Tests\BrowserTestBase $browser */
$dir = $browser->getDrupalRoot('extra');
?>
-----
<?php

// Unrelated class — type guard prevents transformation.
/** @var \SomeOtherClass $obj */
$dir = $obj->getDrupalRoot();

// BuildTestBase overrides getDrupalRoot() with a non-deprecated implementation; must not change.
/** @var \Drupal\BuildTests\Framework\BuildTestBase $build */
$dir = $build->getDrupalRoot();

// Method called with arguments — must not change (deprecated method is parameterless).
/** @var \Drupal\Tests\BrowserTestBase $browser */
$dir = $browser->getDrupalRoot('extra');
?>
