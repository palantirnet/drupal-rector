# Rector for Drupal

Automate fixing deprecated Drupal code.

## Introduction

https://www.palantir.net/blog/jumpstart-your-drupal-9-upgrade-drupal-rector

## Issues are managed on d.o

https://www.drupal.org/project/rector

## Installation

Install the library inside a drupal project.

```bash
$ composer require --dev palantirnet/drupal-rector
```

Create / copy / symlink a `rector.yml` file in the Drupal root.

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

You can find more information about Rector [here](https://github.com/rectorphp/rector).

## Development

We recommend using our `drupal-rector-sandbox` development environment [https://github.com/palantirnet/drupal-rector-sandbox](https://github.com/palantirnet/drupal-rector-sandbox)

Alternatively, you can use your existing Drupal project and follow the instructions in [README](https://github.com/palantirnet/drupal-rector-sandbox/blob/master/README.md#developing-with-drupal-rector)

## Credits

Current development is sponsored by [Palantir.net](https://www.palantir.net).<br/>
Initial development is sponsored by [Pronovix](https://pronovix.com).
