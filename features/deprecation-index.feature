Feature: deprecation-index.yml
  As a Drupal developer
  I want the deprecation-index.yml file to parse cleanly
  So that other systems can reference this file

  # Most often, quotes are not escaped.
  Scenario: deprecation-index.yml is valid YAML
    When I examine the "deprecation-index.yml" file
    Then The file is valid YAML

  # Unicode characters sometimes get added when copy & pasting.
  # They are not intended and don't match as easily.
  Scenario: deprecation-index.yml only uses ASCII characters
    When I examine the "deprecation-index.yml" file
    Then The file only uses ASCII characters.
