<?php

declare(strict_types=1);

namespace DrupalRector\Set;

/**
 * Version-independent sets.
 *
 * @see \DrupalRector\Set\Drupal8SetList
 * @see \DrupalRector\Set\Drupal9SetList
 * @see \DrupalRector\Set\Drupal10SetList
 * @see \DrupalRector\Set\Drupal11SetList
 * @see \DrupalRector\Set\Drupal12SetList
 */
final class DrupalSetList
{
    /**
     * Every rule, each bound to the exact `drupal/core` version its deprecation
     * was introduced in.
     *
     * Rector activates only the rules whose constraint the installed
     * `drupal/core` satisfies, so this single set replaces hand-picking the
     * per-minor `Drupal*SetList` constants. Rules for a Drupal version that is
     * not installed never run, which is what makes the breaking renames safe to
     * include here.
     */
    public const COMPOSER_BASED = __DIR__.'/../../config/composer-based.php';

    /**
     * Five upstream Rector rules for PHP 8.4/8.5 deprecations and PHPUnit
     * mock stubs, registered individually. Opt-in; not part of
     * `COMPOSER_BASED` or any `Drupal*SetList` set.
     *
     * Every rewrite runs on PHP 8.1+ and PHPUnit 9.6+, so it fits code that
     * supports Drupal 10.4 and later. It is not for code that still supports
     * PHP 8.0 or older.
     *
     * The set raises Rector's target PHP version to 8.4 when it is lower, which
     * `ExplicitNullableParamTypeRector` needs to run.
     *
     * @see \DrupalRector\Tests\Set\UpstreamPhpPhpunitSet\UpstreamPhpPhpunitSetTest
     */
    public const UPSTREAM_PHP_PHPUNIT = __DIR__.'/../../config/upstream-php-phpunit.php';
}
