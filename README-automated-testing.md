# Automated testing

## Unit testing

The Drupal Rector project has PHPUnit tests, extending the testing suite functionality provided by Rector. This allows
the project to have confidence in the written Rector rules. To run the unit tests, there are different system requirements
than if you ran Drupal Rector against your Drupal site.

The developer dependencies require `rector/rector-src` which requires PHP 8.

To run the PHPUnit tests:

* Clone your fork of the repository
* Run `composer install`
* Run `php vendor/bin/phpunit`

See the `.github/workflows/phpunit.yml` workflow for an example.

### Writing a PHPUnit test

For now, please see the example in `tests/src/Rector/Deprecation/DatetimeStorageTimezoneRector`

## Installation test

GitHub Action workflows test that this package can be installed. See the workflows in `.github/workflows`.

## Rector functional testing

The functional test takes the `rector_examples` directory in this package and copies it to a Drupal code base.

The workflow then runs `vendor/bin/rector process web/modules/custom/rector_examples` to apply all of the appropriate Rector rules.

Then, the `diff` command is run to verify the changes match the expected results as found in the `rector_examples_updated` directory.

To add new tests, create a sample file in `rector_examples` and a copy with the expected changes into `rector_examples_updated`.
