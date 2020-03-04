# Rector for Drupal

Apply automatic fixes on your Drupal code.

## Introduction

https://www.palantir.net/blog/jumpstart-your-drupal-9-upgrade-drupal-rector

## Issues are managed on d.o

https://www.drupal.org/project/rector

## Installation

Install the library inside a drupal project.

```bash
$ composer require --dev palantirnet/drupal-rector
```

Create a rector.yml file in the Drupal root.

```yml
imports:
  - { resource: "vendor/palantirnet/drupal-rector/config/drupal-8/drupal-8-all-deprecations.yml" }
  # includes:
  # - { resource: "vendor/palantirnet/drupal-rector/config/drupal-8/drupal-8.5-deprecations.yml" }
  # - { resource: "vendor/palantirnet/drupal-rector/config/drupal-8/drupal-8.6-deprecations.yml" }
  # - { resource: "vendor/palantirnet/drupal-rector/config/drupal-8/drupal-8.7-deprecations.yml" }

parameters:
  autoload_paths:
    - 'web/core'
    - 'web/core/modules'
    - 'web/modules'
    - 'web/profiles'
  file_extensions:
    - module
    - theme
    - install
    - profile
    - inc
    - engine

services: ~
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

## Development

We recommend using our `drupal-rector-sandbox` development environment [https://github.com/palantirnet/drupal-rector-sandbox](https://github.com/palantirnet/drupal-rector-sandbox)

Alternatively, you can use your existing Drupal project and follow the instructions in [README](https://github.com/palantirnet/drupal-rector-sandbox/blob/master/README.md#developing-with-drupal-rector)

## Roadmap

This is just a POC at this moment but it has a great potential to become an actual development tool for Drupal 8.

*Do you have an idea about what else this tool could do? Please share it in the issue queue. Pull requests are also warmly welcomed!*

## Credits

Initial development is sponsored by [Pronovix](https://pronovix.com).<br/>
Additional development is sponsored by [Palantir.net](https://www.palantir.net).
