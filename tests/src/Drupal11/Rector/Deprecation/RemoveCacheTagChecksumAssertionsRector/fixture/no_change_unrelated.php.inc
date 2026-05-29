<?php

// Untyped receiver — PHPStan cannot confirm PerformanceTestTrait, so no change.
$unrelated->assertMetrics([
    'CacheGetCount' => 5,
    'CacheTagChecksumCount' => 38,
    'CacheTagIsValidCount' => 43,
    'CacheTagInvalidationCount' => 0,
], $performance_data);
