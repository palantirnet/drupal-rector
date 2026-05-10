<?php

/** @var \Drupal\Tests\block_content\Traits\BlockContentCreationTrait $testBase */
$testBase->createBlockContentType('basic', TRUE);
?>
-----
<?php

/** @var \Drupal\Tests\block_content\Traits\BlockContentCreationTrait $testBase */
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.1.0', fn() => $testBase->createBlockContentType(['id' => 'basic'], TRUE), fn() => $testBase->createBlockContentType('basic', TRUE));
?>
