# Rector for Drupal

Automate fixing deprecated Drupal code.

## Introduction

https://www.palantir.net/blog/jumpstart-your-drupal-9-upgrade-drupal-rector

## Issues are managed on drupal.org

https://www.drupal.org/project/rector

For contribution suggestions, please see the later section of this document.

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

## Contribution Suggestions

Thanks for your interest in contributing!

Our goal is to make contributing to this project easy for people. While we've made certain architectural decisions here to hopefully achieve that goal, it's a work in progress and feedback is appreciated.

### Adding a Rector rule

If you would like to submit a Rector rule, we are looking for the following:

- A Rector rule class, see `/src/Rector/Deprecation` for existing rules
- An example file or files that show(s) the before and after, see `/rector_examples`
- An updated configuration file that registers the Rector rule, see `/config/drupal-8`
- A listing in the index file, see `/deprecation-index.yml`

The index file is used in part to provide automated updates to https://dev.acquia.com/drupal9/deprecation_status/errors which is a helpful way to track coverage. The `PHPStan` messages are listed there as well as in the change record comments throughout the Drupal codebase.

### A few tips

We would like one Rector rule per deprecation. Some deprecations include updating multiple things and those would be separate rules.

To avoid duplication, we have created base classes for simple repeated patterns where possible. These end in `Base.php`. In many of these rules, you will extend the base class, define class properties, add a class comment, and define the definition.

Rector supports passing parameters to rules and you can also define your rules in a variety of ways. To avoid confusion for new developers, we're trying to avoid these advanced features so that someone with limited familiarity with the tool can easily determine where things are located and what they are doing. If the copy & paste challenge isn't worth this trade-off, we can re-evaluate it as we go. Suggestions appreciated.

## Credits

Current development is sponsored by [Palantir.net](https://www.palantir.net).<br/>
Initial development is sponsored by [Pronovix](https://pronovix.com).
