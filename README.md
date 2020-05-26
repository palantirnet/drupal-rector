# Drupal Rector

Automate fixing deprecated Drupal code.

## Status

![local_package_functional_tests](https://github.com/palantirnet/drupal-rector/workflows/local_package_functional_tests/badge.svg)
![local_package_run_rector](https://github.com/palantirnet/drupal-rector/workflows/local_package_run_rector/badge.svg)
![packagist_package_run_rector](https://github.com/palantirnet/drupal-rector/workflows/packagist_package_run_rector/badge.svg)

## Introduction

You can read more details in the following blog post:

https://www.palantir.net/blog/jumpstart-your-drupal-9-upgrade-drupal-rector

## Documentation

Development guides, individual deprecation overviews, and other resources can be found here:

https://www.palantir.net/rector

## Scope and limitations

The development of this tool is prioritized by the percieved impact of the deprecations and updates. There are many deprecations that often involve several components and for each of these there are several ways to address the deprecation.

We've tried to determine impact based on:
- The use of the deprecated functionality in the contributed modules on Drupal.org
- If there are simple to develop ways to fix the deprecation

So, high impact (the code works in newer versions of Drupal for a large number of people), low effort (we can develop the rule based on our knowledge of Rector).

### Common limitations

Known limitations are listed in the comment documentation for each rule.

Common limitations include:
- Using static calls like `Drupal->service('my_service')->myMethod();` rather than injecting the service into the class
- Skipping complex use cases, such as when optional arguments are passed as variables

Our hope is that as we learn more about Rector, we may be able to update these rules to add these features.

## Issues are managed on [drupal.org](https://www.drupal.org/project/rector)

https://www.drupal.org/project/rector

For contribution suggestions, please see the later section of this document.

## Installation

### Install Drupal Rector inside a Drupal project.

```bash
$ composer require --dev palantirnet/drupal-rector
```

### Create a configuration file in your project

You will need to have a `rector.yml` configuration in the root of your repository. This should sit beside your document root such as `web` or `docroot`.

This project provides starting files that should handle most use cases.

If your document root directory is `web`, you can copy the `rector-config-web-dir.yml`

```bash
cp vendor/palantirnet/drupal-rector/rector-config-web-dir.yml rector.yml
```

If your document root directory is `docroot`, you can copy the `rector-config-docroot-dir.yml`

```bash
cp vendor/palantirnet/drupal-rector/rector-config-docroot-dir.yml rector.yml
```

If your document root directory is something else you will need to manually copy and edit `rector.yml`.

Replace the `web` in these paths with your document root.

```
parameters:
  autoload_paths:
    - 'web/core'
    - 'web/core/modules'
    - 'web/modules'
    - 'web/profiles'
```

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

We recommend using our `drupal-rector-sandbox` development environment [https://github.com/palantirnet/drupal-rector-sandbox](https://github.com/palantirnet/drupal-rector-sandbox)

Alternatively, you can use your existing Drupal project and follow the instructions in [README](https://github.com/palantirnet/drupal-rector-sandbox/blob/master/README.md#developing-with-drupal-rector)

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

## Credits

Current development is sponsored by [Palantir.net](https://www.palantir.net).<br/>
Initial development is sponsored by [Pronovix](https://pronovix.com).
