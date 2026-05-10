<?php

/** @var \Drupal\Tests\block_content\Traits\BlockContentCreationTrait $testBase */
$testBase->createBlockContentType('basic', TRUE);
$testBase->createBlockContentType('basic');
?>
-----
<?php

/** @var \Drupal\Tests\block_content\Traits\BlockContentCreationTrait $testBase */
$testBase->createBlockContentType(['id' => 'basic'], TRUE);
$testBase->createBlockContentType(['id' => 'basic']);
?>
