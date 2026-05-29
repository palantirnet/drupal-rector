<?php

/** @var \Drupal\Tests\PerformanceTestTrait $test */
$test->assertMetrics([
    'CacheGetCount' => 5,
    'CacheTagChecksumCount' => 38,
    'CacheTagIsValidCount' => 43,
    'CacheTagInvalidationCount' => 0,
], $performance_data);
?>
-----
<?php

/** @var \Drupal\Tests\PerformanceTestTrait $test */
$test->assertMetrics([
    'CacheGetCount' => 5,
    'CacheTagInvalidationCount' => 0,
], $performance_data);
?>
