<?php

/** @var \Drupal\Tests\BrowserTestBase $browser */
$dir = $browser->getDrupalRoot() . '/core/tests/fixtures';

/** @var \Drupal\KernelTests\KernelTestBase $kernel */
$path = $kernel->getDrupalRoot();

/** @var \Drupal\Tests\UnitTestCase $unit */
echo $unit->getDrupalRoot();
?>
-----
<?php

/** @var \Drupal\Tests\BrowserTestBase $browser */
$dir = $browser->root . '/core/tests/fixtures';

/** @var \Drupal\KernelTests\KernelTestBase $kernel */
$path = $kernel->root;

/** @var \Drupal\Tests\UnitTestCase $unit */
echo $unit->root;
?>
