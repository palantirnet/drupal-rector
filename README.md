# Rector for Drupal

Automate fixing deprecated Drupal code.

## Introduction

https://www.palantir.net/blog/jumpstart-your-drupal-9-upgrade-drupal-rector

## Issues are managed on drupal.org

https://www.drupal.org/project/rector

## Installation

### Install Drupal Rector inside a Drupal project.

```bash
$ composer require --dev palantirnet/drupal-rector
```

_If you have installation issues, you may need to upgrade `phpstan/phpstan` with Composer first._

### Create a configuration file in your project

You will need to have a `rector.yml` configuration in the root of your repository. This should sit beside your document root such as `web` or `docroot`.

You can copy the file example `rector.yml` file from this repository.

#### Edit the configuration file if needed

If you are using `docroot` as your document root instead of `web`, you will need to edit the `rector.yml` file from this repository to point to `docroot` instead of `web` under `parameters.autoload_paths`.

```yml
parameters:
  autoload_paths:
    - 'web/core'
    - 'web/core/modules'
    - 'web/modules'
    - 'web/profiles'
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
