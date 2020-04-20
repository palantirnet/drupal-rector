# Rector for Drupal

Automate fixing deprecated Drupal code.

## Introduction

https://www.palantir.net/blog/jumpstart-your-drupal-9-upgrade-drupal-rector

## Issues are managed on [drupal.org](https://www.drupal.org/project/rector)

https://www.drupal.org/project/rector

## Installation

### Install Drupal Rector inside a Drupal project.

```bash
$ composer require --dev palantirnet/drupal-rector
```

### Create a configuration file in your project

A `rector.yml` file with drupal-specific configuration is needed.

If your document root directory is `web`, run this:
```bash
cp vendor/palantirnet/drupal-rector/rector-config-web-dir.yml rector.yml
```

If your document root directory is `docroot`, run this:
```bash
cp vendor/palantirnet/drupal-rector/rector-config-docroot-dir.yml rector.yml
```

(If your document root directory is something else you will need to manually edit `rector.yml`)

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

## Troubleshooting

_If you have installation issues, you may need to upgrade `phpstan/phpstan` with Composer first._


## Credits

Current development is sponsored by [Palantir.net](https://www.palantir.net).<br/>
Initial development is sponsored by [Pronovix](https://pronovix.com).
