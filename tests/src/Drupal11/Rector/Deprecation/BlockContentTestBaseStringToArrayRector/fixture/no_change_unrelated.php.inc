<?php

// Unrelated class — type guard prevents transformation.
/** @var \SomeOtherClass $obj */
$obj->createBlockContentType('basic', TRUE);

// Two string args — InlineBlockTestBase pattern, must not change.
/** @var \Drupal\Tests\block_content\Traits\BlockContentCreationTrait $testBase */
$testBase->createBlockContentType('basic', 'Basic block');
?>
-----
<?php

// Unrelated class — type guard prevents transformation.
/** @var \SomeOtherClass $obj */
$obj->createBlockContentType('basic', TRUE);

// Two string args — InlineBlockTestBase pattern, must not change.
/** @var \Drupal\Tests\block_content\Traits\BlockContentCreationTrait $testBase */
$testBase->createBlockContentType('basic', 'Basic block');
?>
