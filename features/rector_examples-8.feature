@8
Feature: Drupal 8 rector_examples module
  As a Drupal developer
  I want rector_example deprecations to be updated by Drupal Rector
  So that I don't have to update them manually

  Scenario: Verify rector_examples updates match rector_examples_updated.
    Given I install the Drupal Rector configuration file "default-config-templates/drupal-8/rector-8-web.yml"
    And I create a test copy of the folder "example-modules/drupal-8/rector_examples"
    When I run Drupal Rector on the test folder
    Then the test folder matches "example-modules/drupal-8/rector_examples_updated"
