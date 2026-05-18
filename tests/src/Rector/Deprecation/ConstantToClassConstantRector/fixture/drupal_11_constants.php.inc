<?php

// Drupal 11.2: REQUIREMENT_* → RequirementSeverity enum cases
$a = REQUIREMENT_INFO;
$b = REQUIREMENT_OK;
$c = REQUIREMENT_WARNING;
$d = REQUIREMENT_ERROR;

// Drupal 11.2: LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN → \Drupal::TRANSLATION_DEFAULT_SERVER_PATTERN
$pattern = LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN;

// Drupal 11.3: JSONAPI_FILTER_AMONG_* → JsonApiFilter class constants
$filter1 = JSONAPI_FILTER_AMONG_ALL;
$filter2 = JSONAPI_FILTER_AMONG_PUBLISHED;
$filter3 = JSONAPI_FILTER_AMONG_ENABLED;
$filter4 = JSONAPI_FILTER_AMONG_OWN;

?>
-----
<?php

// Drupal 11.2: REQUIREMENT_* → RequirementSeverity enum cases
$a = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\Core\Extension\Requirement\RequirementSeverity::Info, fn() => REQUIREMENT_INFO);
$b = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\Core\Extension\Requirement\RequirementSeverity::OK, fn() => REQUIREMENT_OK);
$c = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\Core\Extension\Requirement\RequirementSeverity::Warning, fn() => REQUIREMENT_WARNING);
$d = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal\Core\Extension\Requirement\RequirementSeverity::Error, fn() => REQUIREMENT_ERROR);

// Drupal 11.2: LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN → \Drupal::TRANSLATION_DEFAULT_SERVER_PATTERN
$pattern = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => \Drupal::TRANSLATION_DEFAULT_SERVER_PATTERN, fn() => LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN);

// Drupal 11.3: JSONAPI_FILTER_AMONG_* → JsonApiFilter class constants
$filter1 = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal\jsonapi\JsonApiFilter::AMONG_ALL, fn() => JSONAPI_FILTER_AMONG_ALL);
$filter2 = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal\jsonapi\JsonApiFilter::AMONG_PUBLISHED, fn() => JSONAPI_FILTER_AMONG_PUBLISHED);
$filter3 = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal\jsonapi\JsonApiFilter::AMONG_ENABLED, fn() => JSONAPI_FILTER_AMONG_ENABLED);
$filter4 = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal\jsonapi\JsonApiFilter::AMONG_OWN, fn() => JSONAPI_FILTER_AMONG_OWN);

?>
