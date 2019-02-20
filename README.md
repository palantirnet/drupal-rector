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
  # Import drupal8-php71.yml ruleset if your module's minimum requirement
  # is PHP >= 7.1.
  - { resource: "vendor/mxr576/drupal8-rector/config/drupal8.yml" }

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

#### Some ideas:
* Automatically replace all usage of deprecated functions/methods
* Ensure [MODULE_NAME].api.php defines all alter hooks that a module defines (with up-to-date parameter list).

*Do you have an idea? Please share it. Pull requests are also warmly welcomed!*
