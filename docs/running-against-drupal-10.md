# Running Drupal Rector against a Drupal 10 project

Drupal Rector targets Drupal 11, but it is just as useful **on a Drupal 10 site** to clean up
deprecations *before* you upgrade. Because rules are gated on the **installed** Drupal version,
running on a D10 site fixes everything deprecated up to that version and leaves not-yet-deprecated
D11-only APIs alone — exactly what you want when preparing for D11.

There are two ways to run it. Try the direct install first; fall back to the standalone runner only
if your project's dev dependencies conflict.

---

## Option A — Install directly in the project (simplest)

If your project does **not** pin PHPStan 1, install Drupal Rector as a normal dev dependency:

```bash
$ composer require --dev "palantirnet/drupal-rector:^1.0@alpha"
$ cp vendor/palantirnet/drupal-rector/rector.php .
$ vendor/bin/rector process web/modules/custom --dry-run
```

This is the standard flow documented in the [README](../README.md#installation). Drop `--dry-run`
to apply the changes.

### When this fails: the PHPStan conflict

`rector/rector` (v2) requires **PHPStan ^2**. If your project also requires PHPStan **1** — most
commonly through [`mglaman/phpstan-drupal`](https://github.com/mglaman/phpstan-drupal) or
`drupal/core-dev` on Drupal 10 — Composer cannot resolve both at once:

```
rector/rector ... requires phpstan/phpstan ^2 ... but it conflicts with your root
composer.json require (^1...).
```

You don't need to remove your existing PHPStan tooling. Use Option B instead.

---

## Option B — Run from a standalone project (no dependency conflict)

Install Drupal Rector in its **own** Composer project, separate from the site under analysis. That
project carries Rector 2 + PHPStan 2 in its own `vendor/`; your site's PHPStan 1 is never installed
alongside it, so there is nothing to conflict. Rector reads your site's code by path and loads its
classes with `--autoload-file`.

### 1. Create the runner project

Anywhere outside your site, e.g. `~/drupal-rector-runner`:

```bash
$ mkdir ~/drupal-rector-runner && cd ~/drupal-rector-runner
$ composer init --no-interaction --name=local/drupal-rector-runner
$ composer config minimum-stability dev
$ composer config prefer-stable true
$ composer require "palantirnet/drupal-rector:^1.0@alpha" "rector/rector:^2"
```

### 2. Add a runner config

Create `rector.php` in the runner project. **Point `TARGET_SITE` at your Drupal site** and note
the two differences from a normal in-project config (both explained below):

```php
<?php

declare(strict_types=1);

use DrupalRector\Set\Drupal8SetList;
use DrupalRector\Set\Drupal9SetList;
use DrupalRector\Set\Drupal10SetList;
use DrupalRector\Set\Drupal11SetList;
use DrupalRector\Services\DrupalRectorSettings;
use Rector\Config\RectorConfig;

// Absolute path to the root of the Drupal site you want to analyse.
const TARGET_SITE = '/path/to/your/drupal-site';

return static function (RectorConfig $rectorConfig): void {
    // (1) Load the PER-VERSION sets, not the DRUPAL_10 / DRUPAL_11 aggregators.
    $rectorConfig->sets([
        Drupal8SetList::DRUPAL_80, Drupal8SetList::DRUPAL_81, Drupal8SetList::DRUPAL_82,
        Drupal8SetList::DRUPAL_83, Drupal8SetList::DRUPAL_84, Drupal8SetList::DRUPAL_85,
        Drupal8SetList::DRUPAL_86, Drupal8SetList::DRUPAL_87, Drupal8SetList::DRUPAL_88,
        Drupal9SetList::DRUPAL_90, Drupal9SetList::DRUPAL_91, Drupal9SetList::DRUPAL_92,
        Drupal9SetList::DRUPAL_93, Drupal9SetList::DRUPAL_94,
        Drupal10SetList::DRUPAL_100, Drupal10SetList::DRUPAL_101,
        Drupal10SetList::DRUPAL_102, Drupal10SetList::DRUPAL_103,
        Drupal11SetList::DRUPAL_110, Drupal11SetList::DRUPAL_111, Drupal11SetList::DRUPAL_112,
        Drupal11SetList::DRUPAL_113, Drupal11SetList::DRUPAL_114,
    ]);

    // Enable BC wrapping so rewritten code still runs on your current Drupal 10.
    $rectorConfig->singleton(DrupalRectorSettings::class, fn () =>
        (new DrupalRectorSettings())->enableBackwardCompatibility());

    // Locate the docroot of the EXTERNAL site (filesystem scan — must be the
    // plain DrupalFinder, not DrupalFinderComposerRuntime).
    $drupalFinder = new DrupalFinder\DrupalFinder();
    $drupalFinder->locateRoot(TARGET_SITE);
    $drupalRoot = $drupalFinder->getDrupalRoot();

    $rectorConfig->autoloadPaths([
        $drupalRoot . '/core',
        $drupalRoot . '/modules',
        $drupalRoot . '/profiles',
        $drupalRoot . '/themes',
    ]);

    $rectorConfig->fileExtensions(['php', 'module', 'theme', 'install', 'profile', 'inc', 'engine']);
    $rectorConfig->importNames(true, false);
    $rectorConfig->importShortClasses(false);
};
```

### 3. Run it

Point Rector at the code to analyse and load the **site's** autoloader so type-aware rules can
resolve Drupal classes:

```bash
$ vendor/bin/rector process /path/to/your/drupal-site/web/modules/custom \
    --dry-run \
    --autoload-file=/path/to/your/drupal-site/vendor/autoload.php
```

Drop `--dry-run` to write the changes into your site. Because the files live in your site's
checkout, review and commit them there as usual.

---

## Why the two differences in the runner config

- **(1) Per-version sets instead of the `DRUPAL_10` / `DRUPAL_11` aggregators.** The aggregator sets
  register an extra bootstrap file that fixes up Drupal's PHPUnit test-namespace autoloading. That
  bootstrap assumes Rector is installed *inside* the site (it resolves `drupal/core` through
  `Composer\InstalledVersions`) and aborts with `Package "drupal/core" is not installed` when run
  from a standalone project. The per-version sets contain the same rules without that bootstrap,
  and `--autoload-file` already gives Rector the class reflection it needs.

- **`--autoload-file`** must point at your **site's** `vendor/autoload.php`. Without it, type-aware
  rules (those that check the type of a method's receiver) can't resolve your site's Drupal classes
  and won't fire.

---

## What gets fixed

With BC wrapping enabled, each rewrite is wrapped so it keeps working on your current Drupal 10
while also being correct on the newer API, e.g.:

```php
// before
return format_size($bytes);

// after
return \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(
    \Drupal::VERSION, '10.2.0',
    fn() => \Drupal\Core\StringTranslation\ByteSizeMarkup::create($bytes),
    fn() => format_size($bytes),
);
```

Rules whose deprecation was introduced **after** your installed Drupal version are skipped, so a
run on Drupal 10 will not pull in Drupal 11-only changes. When you later move to Drupal 11, run
Rector again to pick up the remaining rewrites.
