Feature: Drupal::url()
  As a Drupal developer
  I want Drupal::url() deprecations to be updated by Drupal Rector
  So that I don't have to update them manually

  Scenario: Verify static file is updated as expected.
    Given I create a test file from "rector_examples/drupal_url.php"
    When I run Drupal Rector on the test file
    And I examine the test file
    Then the test file matches "rector_examples/drupal_url_updated.php"

  Scenario: Verify class file is updated as expected.
    Given I create a test file from "rector_examples/src/DrupalURLStatic.php"
    When I run Drupal Rector on the test file
    And I examine the test file
    Then the test file matches "rector_examples/src/DrupalURLStaticUpdated.php"
