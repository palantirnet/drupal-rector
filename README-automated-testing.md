# Automated testing

## Installation test

GitHub Action workflows test that this package can be installed. See the workflows in `.github/workflows`.

## Rector functional testing

The functional test takes the `rector_examples` directory in this package and copies it to a Drupal code base.

The workflow then runs `vendor/bin/rector process web/modules/custom/rector_examples` to apply all of the appropriate Rector rules.

Then, the `diff` command is run to verify the changes match the expected results as found in the `rector_examples_updated` directory.

To add new tests, create a sample file in `rector_examples` and a copy with the expected changes into `rector_examples_updated`.
