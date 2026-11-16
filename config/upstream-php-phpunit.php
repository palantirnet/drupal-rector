<?php

declare(strict_types=1);

/**
 * Upstream PHP and PHPUnit rules that Drupal contrib code benefits from.
 *
 * Opt-in via `DrupalSetList::UPSTREAM_PHP_PHPUNIT`. Not loaded by any other set,
 * including `DrupalSetList::COMPOSER_BASED`.
 *
 * Five Rector rules are registered one by one, not through Rector's `php81` /
 * `php84` / `php85` or rector-phpunit code-quality sets, which also carry rules
 * that change behaviour or need a newer PHP version than Drupal 10.4 (PHP 8.1)
 * and PHPUnit 9.6 provide. All five rewrite to code that runs on PHP 8.1 and
 * PHPUnit 9.6 as well as on the newest versions:
 *
 *  - ExplicitNullableParamTypeRector: `Foo $x = null` to `?Foo $x = null`
 *    (nullable types since PHP 7.1, `T|null` since 8.0). Silences the PHP 8.4
 *    implicit-nullable deprecation.
 *  - RemoveReflectionSetAccessibleCallsRector: drops `setAccessible()`, a no-op
 *    since PHP 8.1.
 *  - RenameCastRector: `(boolean)`, `(integer)`, `(double)` and `(binary)` to
 *    `(bool)`, `(int)`, `(float)` and `(string)`. Deprecated in PHP 8.5.
 *  - RemoveFuncCallRector: drops `curl_close()`, `curl_share_close()`,
 *    `finfo_close()`, `imagedestroy()` and `xml_parser_free()`, no-ops since
 *    PHP 8.0 and deprecated in PHP 8.5.
 *  - UseSpecificWillMethodRector (rector-phpunit, bundled with rector/rector):
 *    `->will($this->returnValue($x))` to `->willReturn($x)`, and the sibling
 *    stubs. The `willReturn*` methods exist in PHPUnit 9.6, 10.5 and 11.5.
 *
 * The rules only run on code that has to run on PHP 8.1 or newer, which is what
 * every Drupal 10 and 11 release requires. Do not use the set on code that
 * still supports Drupal 9 or PHP 8.0 and older.
 */

use PhpParser\Node\Expr\Cast\Bool_;
use PhpParser\Node\Expr\Cast\Double;
use PhpParser\Node\Expr\Cast\Int_;
use PhpParser\Node\Expr\Cast\String_;
use Rector\Config\RectorConfig;
use Rector\Configuration\Option;
use Rector\Configuration\Parameter\SimpleParameterProvider;
use Rector\Php\PhpVersionResolver\ComposerJsonPhpVersionResolver;
use Rector\Php81\Rector\MethodCall\RemoveReflectionSetAccessibleCallsRector;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;
use Rector\PHPUnit\CodeQuality\Rector\MethodCall\UseSpecificWillMethodRector;
use Rector\Removing\Rector\FuncCall\RemoveFuncCallRector;
use Rector\Renaming\Rector\Cast\RenameCastRector;
use Rector\Renaming\ValueObject\RenameCast;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    // ExplicitNullableParamTypeRector only runs when the target PHP version is
    // 8.4 or newer. The target comes from the `php` constraint in the project's
    // composer.json, which for contrib is usually `>=8.1`, so the rule would
    // never load. Raise the target to 8.4, but never lower it. This controls
    // whether the rule acts, not the syntax it emits: `?Foo` stays valid on 8.1.
    //
    // The raised target applies to every other rule that checks the PHP
    // version as well. An explicit `->withPhpVersion()` in the project's
    // rector.php is applied after this set, so it wins; with a version below
    // 8.4 the nullable rule stays off.
    $currentPhpVersion = SimpleParameterProvider::hasParameter(Option::PHP_VERSION_FEATURES)
        ? SimpleParameterProvider::provideIntParameter(Option::PHP_VERSION_FEATURES)
        : (ComposerJsonPhpVersionResolver::resolve((string) getcwd().'/composer.json') ?? \PHP_VERSION_ID);

    if ($currentPhpVersion < PhpVersion::PHP_84) {
        $rectorConfig->phpVersion(PhpVersion::PHP_84);
    }

    $rectorConfig->rule(ExplicitNullableParamTypeRector::class);
    $rectorConfig->rule(RemoveReflectionSetAccessibleCallsRector::class);

    // Not gated on the PHP version: both act purely on this configuration.
    $rectorConfig->ruleWithConfiguration(RenameCastRector::class, [
        new RenameCast(Bool_::class, Bool_::KIND_BOOLEAN, Bool_::KIND_BOOL),
        new RenameCast(Int_::class, Int_::KIND_INTEGER, Int_::KIND_INT),
        new RenameCast(Double::class, Double::KIND_DOUBLE, Double::KIND_FLOAT),
        new RenameCast(String_::class, String_::KIND_BINARY, String_::KIND_STRING),
    ]);
    $rectorConfig->ruleWithConfiguration(RemoveFuncCallRector::class, [
        'curl_close',
        'curl_share_close',
        'finfo_close',
        'imagedestroy',
        'xml_parser_free',
    ]);

    // rector-phpunit ships inside rector/rector, so there is no extra package
    // to require. Only touches test code, and is not gated on the PHP version.
    $rectorConfig->rule(UseSpecificWillMethodRector::class);
};
