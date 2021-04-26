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

@todo: Provide more information about PhpUnit / PHPStan.

### Adding tests

Tests should be pretty simple. By default, the main test will test the entire `rector_examples` folder and report any differences. Tests can also be made for individual files.

The functional tests make a copy of the file or folder we are going to test, so you don't have to worry about overwriting files in those directories.
