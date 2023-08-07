<?php

declare(strict_types=1);

namespace Drupal\Component\Utility;

if (class_exists('Drupal\Core\Component\DeprecationHelper')) {
    return;
}

final class DeprecationHelper {

    /**
     * Helper to run a callback based on the installed version of Drupal.
     *
     * With this helper, contributed or custom modules can easily run different
     * code paths based on the version of Drupal using callbacks.
     *
     * The below templates help code editors and PHPStan understand the return
     * value of this function.
     *
     * @template Current
     * @template Deprecated
     *
     * @param string $version
     *   Version to check against.
     * @param string $introducedVersion
     *   Version that deprecated the old code path.
     * @param callable(): Current $current
     *   Callback for the current version of Drupal.
     * @param callable(): Deprecated $deprecated
     *   Callback for older versions of Drupal.
     *
     * @return Current|Deprecated
     */
    public static function backwardsCompatibleCall(string $version, string $introducedVersion, callable $current, callable $deprecated): mixed {
        // Normalize the version string when it's a dev version.
        $normalizedVersion = str_ends_with($version, '-dev') ? str_replace(['.x-dev', '-dev'], '.0', $version) : $version;

        return version_compare($normalizedVersion, $introducedVersion, '>=') ? $current() : $deprecated();
    }

}
