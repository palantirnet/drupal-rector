# Drupal Rector

Automate fixing deprecated Drupal code.

## Status

![Functional test: Rector examples](https://github.com/palantirnet/drupal-rector/workflows/functional_test__rector_examples/badge.svg)

## Latest release

[Version 0.15.0 for Drupal 8.x and 9.x deprecations](https://github.com/palantirnet/drupal-rector/tree/0.15.0). Note that Drupal 9 deprecation testing recommends PHP 8.

### Release notes

* The 0.13.0 and higher releases of drupal-rector will include Rector 0.13.8+. The upgrade path should be as simple as re-copying the configuration file. `cp vendor/palantirnet/drupal-rector/rector.php`

*Note that GitHub does not let us have different default homepage and merge branches. If you checked out the project using packagist/composer, read the docs for your version.*

## Introduction

You can read more details in the following blog post:

https://www.palantir.net/blog/jumpstart-your-drupal-9-upgrade-drupal-rector

## Documentation

Development guides, individual deprecation overviews, and other resources can be found here:

https://www.palantir.net/rector

## Scope and limitations

The development of this tool is prioritized by the perceived impact of the deprecations and updates. There are many deprecations that often involve several components and for each of these there are several ways to address the deprecation.

We've tried to determine impact based on:
- The use of the deprecated functionality in the contributed modules on Drupal.org
- If there are simple to develop ways to fix the deprecation

So, high impact (the code works in newer versions of Drupal for a large number of people), low effort (we can develop the rule based on our knowledge of Rector).

### Common limitations

Known limitations are listed in the comment documentation for each rule.

Common limitations include:
- Using static calls like `Drupal->service('my_service')->myMethod();` rather than injecting the service into the class
- Skipping complex use cases, such as when optional arguments are passed as variables
- Handling `use` statements in weird ways. Rector has a global option to handle `use` statements and we think the benefits outweigh the drawbacks such as weird placement or lack of handling of less common patterns.
- Handling doc comments in weird ways, particularly around spacing. Rector uses dependencies that sometimes delete empty comments or remove white space. At this point, Drupal Rector does not intend to modify any doc comments, but Rector ends up doing this.

Our hope is that as we learn more about Rector, we may be able to update these rules to add these features.

## Issues are managed on [drupal.org](https://www.drupal.org/project/rector)

https://www.drupal.org/project/issues/rector

For contribution suggestions, please see the later section of this document.

## Installation

**NOTE**: To have the best experience with Drupal Rector, your Drupal site should be running version 8.9 or higher.

### Install Drupal Rector inside a Drupal project.

```bash
$ composer require --dev palantirnet/drupal-rector
```

### Create a configuration file in your project

You will need to have a `rector.php` configuration in the root of your repository. This should sit beside your document root such as `web` or `docroot`.

This project uses [`webflo/drupal-finder`](https://packagist.org/packages/webflo/drupal-finder) to find your document root that contains Drupal.

To get started, copy the `rector.php` configuration file provided by this package:

```bash
cp vendor/palantirnet/drupal-rector/rector.php .
```

By default, Drupal Rector will fix deprecated code for all versions of Drupal. If you want to change this behavior, modify
the sets used in the `rector.php` config. For example, if your site is still on Drupal 9.3, and you cannot fix deprecations
made in Drupal 9.4, use the following configuration:

```php
$rectorConfig->sets([
    Drupal9SetList::DRUPAL_90,
    Drupal9SetList::DRUPAL_91,
    Drupal9SetList::DRUPAL_92,
    Drupal9SetList::DRUPAL_93,
]);
```

This is more granular than the `Drupal9SetList::DRUPAL_9` set.

## Suggested workflow

1. Analyze your code with Rector and review suggested changes:

```sh
$ vendor/bin/rector process web/modules/contrib/[YOUR_MODULE] --dry-run
```

2. Apply suggested changes:

```sh
$ vendor/bin/rector process web/modules/contrib/[YOUR_MODULE]
```

You can find more information about Rector [here](https://github.com/rectorphp/rector).

## Troubleshooting

### PhpStan composer issues

You may need to upgrade `phpstan/phpstan` with Composer before installing this package.

Rector itself has conflicts with older versions of PhpStan.

### Unable to find Rector rule classes

If you are getting errors like

`[ERROR] Class "DrupalRector\Rector\Deprecation\EntityManagerRector" was not found while loading`

You may need to rebuild your autoload file.

`composer dump-autoload`

### FileLocator::locate() must be compatible with FileLocatorInterface::locate()

If you are getting errors like

```
PHP Fatal error:  Declaration of _HumbugBox3630ef99eac4\Symfony\Component\HttpKernel\Config\FileLocator::locate($file, $currentPath = NULL, $first = true) must be compatible with _HumbugBox3630ef99eac4\Symfony\Component\Config\FileLocatorInterface::locate(string $name, ?string $currentPath = NULL, bool $first = true) in phar:///var/www/html/vendor/rector/rector-prefixed/rector/vendor/symfony/http-kernel/Config/FileLocator.php on line 20
Fatal error: Declaration of _HumbugBox3630ef99eac4\Symfony\Component\HttpKernel\Config\FileLocator::locate($file, $currentPath = NULL, $first = true) must be compatible with _HumbugBox3630ef99eac4\Symfony\Component\Config\FileLocatorInterface::locate(string $name, ?string $currentPath = NULL, bool $first = true) in phar:///var/www/html/vendor/rector/rector-prefixed/rector/vendor/symfony/http-kernel/Config/FileLocator.php on line 20
```

You may need to check that you are
- Running `composer install` from an environment that supports Php 7.2 or greater
- Running Drupal Rector from an environment that supports Php 7.2 or greater

Sometimes people install composer dependencies from one machine (host machine) and run Drupal Rector from another (such as a Lando VM).

If you are having these issues try running Rector from the environment that has Php 7.2 or greater. Drupal Rector does not need a fully functional web server, it only (more or less) needs Php and access to a standard Drupal set of files.

### Iconv error when running Rector in Alpine Docker

If you are getting errors like

`iconv(): Wrong charset, conversion from UTF-8 to ASCII//TRANSLIT//IGNORE is not allowed`

You can fix it in Dockerfile with

```
# fix work iconv library with alphine
RUN apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/community/ --allow-untrusted gnu-libiconv
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php
```

Credits to @zolotov88 in https://github.com/nunomaduro/phpinsights/issues/43#issuecomment-498108857

## Development and contribution suggestions

Thanks for your interest in contributing!

Our goal is to make contributing to this project easy for people. While we've made certain architectural decisions here to hopefully achieve that goal, it's a work in progress and feedback is appreciated.

### Development environment

See the instructions in [README](https://github.com/palantirnet/drupal-rector-sandbox/blob/master/README.md#developing-with-drupal-rector)

### Adding a Rector rule

If you would like to submit a Rector rule, we are looking for the following:

- A Rector rule class, see `/src/Rector/Deprecation` for existing rules
- An example file or files that show(s) the before and after, see `/rector_examples` and `/rector_examples_updated`
- An updated configuration file that registers the Rector rule, see `/config/drupal-8`
- A listing in the index file, see `/deprecation-index.yml`

#### Guides

A few guides are currently available and we encourage people to create additional guides to provide their perspective and help us better understand this tool together.

##### Video guide on creating a rector rule
[https://www.palantir.net/rector/creating-drupal-rector-rule](https://www.palantir.net/rector/creating-drupal-rector-rule)

##### Additional documentation and links
[https://www.palantir.net/rector](https://www.palantir.net/rector)

#### Quick(?) overview

##### Create a Rector rule class

Rector rules should be named after the deprecation, including the class name.

`Drupal::url()` -> `DrupalUrlRector.php`
`drupal_set_message()` -> `DrupalSetMessageRector.php`

We would like one Rector rule per deprecation. Some deprecations include updating multiple things and those would be separate rules.

To avoid duplication, we have created base classes for simple repeated patterns where possible. These end in `Base.php` and are located in `/src/Rector/Deprecation/Base`. In many of these rules, you will extend the base class, define class properties, add a class comment, and define the definition.

Rector supports passing parameters to rules and you can also define your rules in a variety of ways. To avoid confusion for new developers, we're trying to avoid these advanced features so that someone with limited familiarity with the tool can easily determine where things are located and what they are doing. If the copy & paste challenge isn't worth this trade-off, we can re-evaluate it as we go. Suggestions appreciated.

##### Create examples

We are creating pairs of example files.

These should be named the same thing as the deprecation. So, `DrupalUrlRector` has a `rector_examples/drupal_url.php` example. An example `rector_examples_updated/drupal_url.php` should also be created to show the updated code. You can run Drupal Rector on this file to show the update.

Example

`DrupalUrlRector` -> `rector_examples/drupal_url.php` and `rector_examples_updated/drupal_url.php`

If you would like to show how the code is used in a class, you can add the class to the appropriate place in the `/rector_examples/src` or `/rector_examples/test` directories. Most of the examples in the example module are `services` in that they are stand alone classes.

Since these classes can use static calls, dependency injection, or traits to get access to services, constants, etc, we have added more details to some class names. For example, `*Static` to indicate that the class is not using dependency injection.

Example

`DrupalUrlRector` -> `rector_examples/src/DrupalUrlStatic.php` and `rector_examples_updated/src/DrupalUrlStatic.php`

##### Create / Update a configuration file

The configuration files in `/config/drupal-8` are broken down by Drupal minor versions.

Add your Rector rule to the relevant file.

The key is the fully qualified class name of the Rector rule. The key is the yaml null value `~`.

##### Update the index file

The index file is used in part to provide automated updates to https://dev.acquia.com/drupal9/deprecation_status/errors which is a helpful way to track coverage. The `PHPStan` messages are listed there as well as in the change record comments throughout the Drupal codebase.

## Pinning dev dependencies

If there are conflicts with Rector, the package version can be conflicted with `conflict` on `rector/rector`.

For development, the `require-dev` is on `rector/rector-src` which is `dev-main` and that includes the `dev-main` of all
its packages.

To properly pin a development release of `rector-src`:

* Set `rector/rector-src` to `dev-main#COMMIT` where `COMMIT` is the tag commit in `rector-src`
* View the tree for the commit on GitHub and it's `composer/installed.json` file (example https://github.com/rectorphp/rector/blob/0.12.18/vendor/composer/installed.json)
* Use the references to pin `require-dev` dependencies.

## Credits

Current development is sponsored by [Palantir.net](https://www.palantir.net).<br/>
Initial development is sponsored by [Pronovix](https://pronovix.com).
