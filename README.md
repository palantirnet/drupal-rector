# Rector for Drupal

Automate fixing deprecated Drupal code.

## Introduction

https://www.palantir.net/blog/jumpstart-your-drupal-9-upgrade-drupal-rector

## Issues are managed on [drupal.org](https://www.drupal.org/project/rector)

https://www.drupal.org/project/rector

For contribution suggestions, please see the later section of this document.

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

### PhpStan composer issues

You may need to upgrade `phpstan/phpstan` with Composer before installing this package.

Rector itself has conflicts with older versions of PhpStan.

### Unable to find Rector rule classes

If you are getting errors like

`[ERROR] Class "DrupalRector\Rector\Deprecation\EntityManagerRector" was not found while loading`

You may need to rebuild your autoload file.

`composer dump-autoload`

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
