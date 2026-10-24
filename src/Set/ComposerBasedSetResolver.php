<?php

declare(strict_types=1);

namespace DrupalRector\Set;

/**
 * Resolves the Drupal sets to load for an installed `drupal/core` version.
 *
 * Each set is keyed to a `drupal/core` minor and is loaded when the installed
 * core satisfies `^<version>`. The caret is a lower bound, so a site on e.g.
 * 11.4 loads every set from 11.0 up to 11.4 and never a future minor's rules —
 * you only ever get rules for deprecations that are live on the installed core,
 * which is inherently backward-compatibility safe.
 *
 * Because the matched version guarantees the replacement symbols exist on the
 * installed core, the otherwise opt-in "breaking" sets are safe to include here.
 *
 * Replaces the former DrupalSetProvider, as Rector removed its set provider API.
 *
 * @see https://github.com/rectorphp/rector-src/pull/8807
 */
final class ComposerBasedSetResolver
{
    private const BOOTSTRAP_SET = __DIR__.'/../../config/drupal-bootstrap.php';

    /**
     * Minor version => deprecation set file. Add a line when a new minor's
     * deprecation config is created.
     *
     * @var array<string, string>
     */
    private const DEPRECATION_SETS = [
        '10.0' => Drupal10SetList::DRUPAL_100,
        '10.1' => Drupal10SetList::DRUPAL_101,
        '10.2' => Drupal10SetList::DRUPAL_102,
        '10.3' => Drupal10SetList::DRUPAL_103,
        '11.0' => Drupal11SetList::DRUPAL_110,
        '11.1' => Drupal11SetList::DRUPAL_111,
        '11.2' => Drupal11SetList::DRUPAL_112,
        '11.3' => Drupal11SetList::DRUPAL_113,
        '11.4' => Drupal11SetList::DRUPAL_114,
        '12.0' => Drupal12SetList::DRUPAL_120,
    ];

    /**
     * Minor version => breaking set file. Safe to apply automatically here:
     * the version match guarantees the replacement symbol exists on the
     * installed core, so the rewrite cannot fatal.
     *
     * @var array<string, string>
     */
    private const BREAKING_SETS = [
        '11.1' => Drupal11SetList::DRUPAL_111_BREAKING,
        '11.2' => Drupal11SetList::DRUPAL_112_BREAKING,
        '11.3' => Drupal11SetList::DRUPAL_113_BREAKING,
        '11.4' => Drupal11SetList::DRUPAL_114_BREAKING,
    ];

    /**
     * @param string|null $installedCoreVersion e.g. "11.4.0.0" or "11.4.x-dev",
     *                                          null when drupal/core is not installed
     *
     * @return string[] set file paths
     */
    public function resolve(?string $installedCoreVersion): array
    {
        if ($installedCoreVersion === null || preg_match('#^(\d+)\.(\d+)#', $installedCoreVersion, $matches) !== 1) {
            return [];
        }

        $installedMajor = (int) $matches[1];
        $installedMinor = (int) $matches[2];

        $sets = [];
        foreach ([self::DEPRECATION_SETS, self::BREAKING_SETS] as $versionedSets) {
            foreach ($versionedSets as $version => $setFilePath) {
                [$major, $minor] = array_map('intval', explode('.', $version));

                // same as composer's "^<major>.<minor>"
                if ($major === $installedMajor && $minor <= $installedMinor) {
                    $sets[] = $setFilePath;
                }
            }
        }

        // The per-minor configs do not register the bootstrap themselves, so it
        // is added once for any supported major.
        if ($sets !== []) {
            $sets[] = self::BOOTSTRAP_SET;
        }

        return $sets;
    }
}
