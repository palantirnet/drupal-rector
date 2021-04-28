# Automated testing

## Installation test

Github Workflow is used to test that this package can be installed. See `.github`.

## Rector automated functional tests using Behat

No extra tool is used to run the functional test(s).

PhpUnit is used to run unit tests.

PHPStan is used for the static analysis tests.

This uses Linux / MacOS commands, so they need to be run from that environment.

These tests assume that this repository is installed as a local composer package. This is necessary, because we need a full Drupal site to run the Rector tests.

Example setup:
```
# This repository
/drupal-rector
# Drupal
/web/core
/web/index.php
# A Composer vendor directory
/vendor/bin/rector
...
```

### Setup

To run the base PHPUnit tests, which run unit tests against Rectors rules, just run the following:

```
php vendor/bin/phpunit
```

You will see that some tests were skipped. These are the Drupal integration tests. To run those, run the following commands:

```
composer run-script phpunit-drupal8-fixture
```

Now, when you run PHPUnit, the Drupal integration tests will run.

```
php vendor/bin/phpunit
```

### Adding tests

#### Rector Rules PHPUnit tests

We follow the same testing pattern as Rector.

Read their wonderful documentation: https://github.com/rectorphp/rector/blob/main/docs/how_to_add_test_for_rector_rule.md

#### Functional tests

Tests should be pretty simple. By default, the main test will test the entire `rector_examples` folder and report any differences. Tests can also be made for individual files.

The functional tests make a copy of the file or folder we are going to test, so you don't have to worry about overwriting files in those directories.
