# Rector for Drupal 8

Apply automatic fixes on your Drupal 8 code.

Check it in action on [Travis CI](https://travis-ci.org/mxr576/drupal8-rector/builds).

## Installation

Install the library.

```bash
$ composer require --dev mxr576/drupal8-rector
```

Create a rector.yml file in the Drupal 8 root.

```yml
imports:
  - { resource: "vendor/mxr576/drupal8-rector/config/drupal8.yml" }
  - { resource: "vendor/mxr576/drupal8-rector/config/drupal86-deprecations.yml" }
  # Import drupal8-php71.yml ruleset if your module's minimum requirement
  # is PHP >= 7.1.
  # - { resource: "vendor/mxr576/drupal8-rector/config/drupal8-php71.yml" }
  # Enable EXPERIMENTAL rectors.
  # - { resource: "vendor/mxr576/drupal8-rector/config/drupal8-experimental.yml" }

parameters:
  autoload_paths:
    - 'web/core/modules'
    - 'web/modules'
  exclude_paths:
    - '*/tests/*'
    - '*/Tests/*'

services:
    # Optionally enable ReturnTypeDeclarationRector rector if your
    # code is PHP >= 7.1 compatible. It is disabled by default
    # because it may cause problems.
    # Mxr576\Rector\FunctionLike\ReturnTypeDeclarationRectorProxy: ~
```
# Suggested workflow

1. Analyze your code with Rector and review suggested changes:

```sh
$ vendor/bin/rector process web/modules/contrib/[YOUR_MODULE] --dry-run
```

2. Apply suggested changes:

```sh
$ vendor/bin/rector process web/modules/contrib/[YOUR_MODULE]
```

3. Automatically correct code style violations with PHPCBF:

```sh
$ vendor/bin/phpcbf --standard=web/core/phpcs.xml.dist web/modules/contrib/[YOUR_MODULE] -s --colors
```
4. Look for remaining code style violations with PHPCS:

```sh
$ vendor/bin/phpcs --standard=web/core/phpcs.xml.dist web/modules/contrib/[YOUR_MODULE] -s --colors
```

5. Run automated tests to ensure the optimized code is still correct:

```sh
$ vendor/bin/phpunit -c web/core --printer="\Drupal\Tests\Listeners\HtmlOutputPrinter" -v --debug web/modules/contrib/[YOUR_MODULE]/tests
```

You can find more information about Rector [here](https://github.com/rectorphp/rector).

## Known issues

* Rector conflict with the PHPUnit version (^6.5 required by webflo/drupal-core-require-dev package) on the required minimum version from sebastian/diff package. Possible solution: temporarily remove webflo/drupal-core-require-dev package while you are testing this library.

## Roadmap

This is just a POC at this moment but it has a great potential to become an actual development tool for Drupal 8.

*Do you have an idea about what else this tool could do? Please share it in the issue queue. Pull requests are also warmly welcomed!*
