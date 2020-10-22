# Automated testing

## Installation test

Github Actions is used to test that this package can be installed. See `.github`.

## Rector automated functional tests using Behat

Behat (the Php version of Cucumber) is used to run automated tests.

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

The tests are located in `features` with a simple `/features/bootstrap/FeatureContext.php` context file which handles running Rector and comparing files.

### Setup

To run the Behat tests, you will need the setup mentioned above. See `.github/workflows/local_package.yml` for an example of how this is done.

Then run `composer install` to install Behat in this repository's `vendor` directory.

To run tests, run `vendor/bin/behat`.

### Adding tests

Tests should be pretty simple. By default, the main test feature `rector_examples.feature` will test the entire `rector_examples` folder and report any differences. Tests can also be made for individual files.

The Behat tests make a copy of the file or folder we are going to test, so you don't have to worry about overwriting files in those directories.

## Run Github Actions locally

Using [Act](https://github.com/nektos/act) you can run Github Actions locally on your machine.

Install Act:

```bash
brew install act
```

Get list of all Github Actions available:

```bash
act -l
```

Run all Github Actions locally:

```bash
act -P ubuntu-latest=shivammathur/node:latest
```
