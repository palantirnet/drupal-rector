# Automated testing

## Installation test

Github Workflow is used to test that this package can be installed. See `.github`.

## Rector automated tests

* PHPUnit is used to run unit tests.
* PHPStan is used for the static analysis tests.

### Setup

We are currently running our tests on PHP 7.3, you may encounter problems when running on PHP 7.4.

Set up Drupal integration fixtures.

```
composer run-script phpunit-drupal8-fixture
```

### Running tests locally

```
vendor/bin/phpunit
```

If you did not set up the Drupal integration fixtures, this will produce errors and skip a few of the Drupal integration tests. These errors can be ignored, or you can set up the integration as described above.

`DrupalRector\Tests\DrupalIntegrationTest::testIntegration` ... `OutOfBoundsException: Package "rector/rector" is not installed`

### Adding tests

#### Rector Rules PHPUnit tests

We follow the same testing pattern as Rector.

Read their wonderful documentation: https://github.com/rectorphp/rector/blob/main/docs/how_to_add_test_for_rector_rule.md

#### Functional tests

@todo Describe how to run a functional test locally. Right now, it can only be done as part of the Github Action.

Tests should be pretty simple. By default, the main test will test the entire `rector_examples` folder and report any differences. Tests can also be made for individual files.

The functional tests make a copy of the file or folder we are going to test, so you don't have to worry about overwriting files in those directories.
