# Rector for Drupal 8

Apply automatic fixes on your Drupal 8 code.

Check it in action on [Travis CI](https://travis-ci.org/mxr576/drupal8-rector/builds).

## Usage

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

Analyze your code with Rector:

```sh
$ vendor/bin/rector process web/modules/contrib/[YOUR_MODULE] --dry-run
```

You can find more information about Rector [here](https://github.com/rectorphp/rector).

## Roadmap

This is just a POC at this moment but it has a great potential to become an actual tool for Drupal 8 developers.

*Do you have an idea about what else this tool could do? Please share it in the issue queue. Pull requests are also warmly welcomed!*
