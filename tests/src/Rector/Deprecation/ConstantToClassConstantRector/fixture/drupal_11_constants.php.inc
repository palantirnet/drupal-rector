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
$a = \Drupal\Core\Extension\Requirement\RequirementSeverity::Info;
$b = \Drupal\Core\Extension\Requirement\RequirementSeverity::OK;
$c = \Drupal\Core\Extension\Requirement\RequirementSeverity::Warning;
$d = \Drupal\Core\Extension\Requirement\RequirementSeverity::Error;

// Drupal 11.2: LOCALE_TRANSLATION_DEFAULT_SERVER_PATTERN → \Drupal::TRANSLATION_DEFAULT_SERVER_PATTERN
$pattern = \Drupal::TRANSLATION_DEFAULT_SERVER_PATTERN;

// Drupal 11.3: JSONAPI_FILTER_AMONG_* → JsonApiFilter class constants
$filter1 = \Drupal\jsonapi\JsonApiFilter::AMONG_ALL;
$filter2 = \Drupal\jsonapi\JsonApiFilter::AMONG_PUBLISHED;
$filter3 = \Drupal\jsonapi\JsonApiFilter::AMONG_ENABLED;
$filter4 = \Drupal\jsonapi\JsonApiFilter::AMONG_OWN;

?>
