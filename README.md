# Drupal Rector

Automate fixing deprecated Drupal code.

## Status

[![Packagist Version](https://img.shields.io/packagist/v/palantirnet/drupal-rector)](https://packagist.org/packages/palantirnet/drupal-rector) ![Functional tests](https://img.shields.io/github/actions/workflow/status/palantirnet/drupal-rector/functional_test__single_rectors.yml?logo=github&label=Functional%20tests) ![Unit tests](https://img.shields.io/github/actions/workflow/status/palantirnet/drupal-rector/phpunit.yml?logo=github&label=Unit%20tests)  ![PHPStan](https://img.shields.io/github/actions/workflow/status/palantirnet/drupal-rector/phpstan.yml?logo=github&label=PHPStan)

If upgrading from an older version, refresh `rector.php` by copying from the vendor copy: `cp vendor/palantirnet/drupal-rector/rector.php .`

## Introduction

Originally created to automate Drupal 9 upgrades; Drupal 8 and 9 rules are still included for legacy projects. You can read more details in the following blog post:

https://www.palantir.net/blog/jumpstart-your-drupal-9-upgrade-drupal-rector

## Documentation

Development guides and other resources:

https://www.palantir.net/rector

Changelog and release history:

https://github.com/palantirnet/drupal-rector/releases


## Scope and limitations

Drupal 10 and 11 are the primary targets (Drupal 8/9 rules are included for legacy projects). The development of this tool is prioritized by the perceived impact of the deprecations and updates. There are many deprecations that often involve several components and for each of these there are several ways to address the deprecation.

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

**NOTE**: To have the best experience with Drupal Rector, your Drupal site should be running Drupal 10 or higher.

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
the sets used in the `rector.php` config. For example, if your site is still on Drupal 10.3, and you do not want to fix deprecations
made in Drupal 10.4, use the following configuration:

```php
$rectorConfig->sets([
    Drupal10SetList::DRUPAL_100,
    Drupal10SetList::DRUPAL_101,
    Drupal10SetList::DRUPAL_102,
    Drupal10SetList::DRUPAL_103,
]);
```

This is more granular than the `Drupal10SetList::DRUPAL_10` set. Since Drupal 10.1 there is not real reason not to include later versions. It will detect the installed Drupal version and supply BC wrappers as needed if you enable it in the config.

### DrupalRectorSettings

The copied `rector.php` includes a `DrupalRectorSettings` block that controls two behaviours:

**Backward-compatibility wrapping** — when enabled, rule results are wrapped in `DeprecationHelper::backwardsCompatibleCall()` so the code works on both the old and new Drupal API simultaneously. The default in `rector.php` is **disabled** (recommended for most projects). Enable it when you need the output to run on multiple Drupal versions at the same time:

```php
$rectorConfig->singleton(DrupalRectorSettings::class, fn () =>
    (new DrupalRectorSettings())
        ->enableBackwardCompatibility()
);
```

**Minimum supported Drupal version** (contrib modules) — if you are running Rector against a contrib module that must stay compatible with an older Drupal release, set `minimumCoreVersionSupported` so BC wrappers are emitted correctly even when your development environment runs a newer Drupal:

```php
$rectorConfig->singleton(DrupalRectorSettings::class, fn () =>
    (new DrupalRectorSettings())
        ->enableBackwardCompatibility()
        ->setMinimumCoreVersionSupported('10.5.0')
);
```

### Cleaning up BC wrappers (contrib modules)

If you previously used backward-compatibility wrapping and have since raised your module's minimum supported Drupal version, use `DeprecationHelperRemoveRector` to strip the now-redundant wrappers. It replaces each `DeprecationHelper::backwardsCompatibleCall()` with the new API call directly, for any deprecation introduced before your configured minimum version.

```php
use DrupalRector\Rector\Deprecation\DeprecationHelperRemoveRector;
use DrupalRector\Rector\ValueObject\DeprecationHelperRemoveConfiguration;

$rectorConfig->ruleWithConfiguration(DeprecationHelperRemoveRector::class, [
    new DeprecationHelperRemoveConfiguration('10.3.0'),
]);
```

With the above, a wrapper like:

```php
DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '9.1.0',
    fn() => \Drupal::service('password_generator')->generate(),
    fn() => user_password()
);
```

becomes:

```php
\Drupal::service('password_generator')->generate();
```

Wrappers for deprecations introduced at or after your minimum version are left untouched. The rule is commented out in `rector.php` — uncomment and set the version when you are ready to clean up.

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

## Converting hooks to OOP hook classes (run as a separate pass)

Drupal Rector ships `HookConvertRector`, which converts procedural hook
implementations (`mymodule_form_alter()`, …) into a `#[Hook]`-attributed class
under `src/Hook/`. **This rule must be run on its own, as a second pass, after
your normal deprecation run — never in the same configuration as the deprecation
sets.**

To convert hooks, run your deprecation pass first, then point Rector at the
dedicated config that registers only `HookConvertRector`:

```sh
# Pass 1 — fix deprecations in place (your normal config)
$ vendor/bin/rector process web/modules/contrib/[YOUR_MODULE]

# Pass 2 — convert hooks to OOP hook classes
$ vendor/bin/rector process web/modules/contrib/[YOUR_MODULE] \
    --config=vendor/palantirnet/drupal-rector/rector-hook-convert.php
```

**Why two passes?** `HookConvertRector` moves each hook body into a brand-new
`src/Hook/*Hooks.php` file that it writes directly to disk. Rector (2.x) cannot
feed a newly-created file back through the rule pipeline within the same run, so
a deprecated API call that lives inside a hook body would be copied into the new
class **unchanged** if hook conversion ran together with the deprecation sets.
Running deprecations first means the bodies are already fixed before they are
moved. For this reason the example `rector.php` above intentionally does **not**
register `HookConvertRector`.

## Development and contribution suggestions

Thanks for your interest in contributing!

Our goal is to make contributing to this project easy for people. While we've made certain architectural decisions here to hopefully achieve that goal, it's a work in progress and feedback is appreciated.

### Adding a Rector rule

If you would like to submit a Rector rule, we are looking for the following:

- A Rector rule class, see `src/Rector/Deprecation` for existing rules. Copy an existing class as a starting point.
- A test class in `tests/src/Drupal{8,9,10,11}/Rector/` and fixture files in `tests/src/Drupal*/Rector/**/fixture/`
- An updated configuration file that registers the Rector rule, see `config/drupal-{8,9,10,11}/`

#### Guides

A few guides are currently available and we encourage people to create additional guides to provide their perspective and help us better understand this tool together.

##### Additional documentation and links
[https://www.palantir.net/rector](https://www.palantir.net/rector)

#### Quick overview

##### Create a Rector rule class

Rector rules should be named after the deprecation, including the class name.

`Drupal::url()` -> `DrupalUrlRector.php`
`drupal_set_message()` -> `DrupalSetMessageRector.php`

We would like one Rector rule per deprecation. Some deprecations include updating multiple things and those would be separate rules.

All drupal-rector rules extend `AbstractDrupalCoreRector` (found in `src/Rector/AbstractDrupalCoreRector.php`) rather than Rector's own `AbstractRector`. This base class provides three things automatically:

- **Version gating** — skips the rule if the installed Drupal version predates the deprecation via `rectorShouldApplyToDrupalVersion()`
- **BC wrapping** — when backward-compatibility mode is enabled, wraps `Expr`→`Expr` results in `DeprecationHelper::backwardsCompatibleCall()` automatically
- **Configuration pattern** — you implement `refactorWithConfiguration(Node $node, VersionedConfigurationInterface $configuration)` instead of `refactor()`

To avoid duplication, we have created base classes for simple repeated patterns where possible. These end in `Base.php` and are located in `src/Rector/Deprecation/Base`. In many of these rules, you will extend the base class, define class properties, add a class comment, and define the definition.

Rector supports passing parameters to rules and you can also define your rules in a variety of ways. To avoid confusion for new developers, we're trying to avoid these advanced features so that someone with limited familiarity with the tool can easily determine where things are located and what they are doing. If the copy & paste challenge isn't worth this trade-off, we can re-evaluate it as we go. Suggestions appreciated.

##### Create / Update a configuration file

The configuration files in `config/drupal-{8,9,10,11}/` are broken down by Drupal minor versions.

Add your Rector rule to the relevant file. Always add a comment with a link to the issue and change record.

The key is the fully qualified class name of the Rector rule. The key is the yaml null value `~`.

## Pinning dev dependencies

If there are conflicts with Rector, the package version can be conflicted with `conflict` on `rector/rector` and `phpstan/phpstan`.

* View the tree for the commit on GitHub and it's `composer/installed.json` file (example https://github.com/rectorphp/rector/blob/0.12.18/vendor/composer/installed.json)
* Use the reference to pin the `phpstan/phpstan` dependency.

## Credits

Current development is sponsored by [SWIS.nl](https://www.swis.nl).<br/>
Current development is sponsored by [Palantir.net](https://www.palantir.net).<br/>
Initial development is sponsored by [Pronovix](https://pronovix.com).
