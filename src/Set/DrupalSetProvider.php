<?php

declare(strict_types=1);

namespace DrupalRector\Set;

use Rector\Set\Contract\SetInterface;
use Rector\Set\Contract\SetProviderInterface;
use Rector\Set\ValueObject\ComposerTriggeredSet;

/**
 * Provides composer-based Drupal sets.
 *
 * Deprecated in favour of \DrupalRector\Set\DrupalSetList::COMPOSER_BASED, but
 * kept registered so existing `withSetProviders(DrupalSetProvider::class)`
 * configs keep working. Rector deprecated
 * \Rector\Set\Contract\SetProviderInterface and
 * \Rector\Set\ValueObject\ComposerTriggeredSet, which this class is built on, in
 * favour of binding each rule to the package version its target API is available
 * from — which the composer-based set does at a per-rule rather than per-minor
 * granularity.
 *
 * Each set is keyed to a `drupal/core` minor and is loaded automatically when
 * the installed core satisfies `^<version>` (see
 * \Rector\Set\ValueObject\ComposerTriggeredSet). The caret is a lower bound, so
 * a site on e.g. 11.4 loads every set from 11.0 up to 11.4 and never a future
 * minor's rules — you only ever get rules for deprecations that are live on the
 * installed core, which is inherently backward-compatibility safe.
 *
 * Because the matched version guarantees the replacement symbols exist on the
 * installed core, the otherwise opt-in "breaking" sets are safe to include here,
 * but they are kept in their own group so they are not loaded by the plain
 * "drupal" group.
 *
 * @see https://github.com/rectorphp/rector/issues/9778
 * @deprecated since 1.2.0, use \DrupalRector\Set\DrupalSetList::COMPOSER_BASED instead
 */
final class DrupalSetProvider implements SetProviderInterface
{
    /**
     * Must match \Rector\Set\Enum\SetGroup::DRUPAL once that constant lands.
     */
    private const GROUP_NAME = 'drupal';

    private const PACKAGE_NAME = 'drupal/core';

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
     * Lowest minor of each supported major. The bootstrap set is matched here
     * so it loads exactly once for any site in that major (the `X.0` set is
     * always loaded by cumulative downward matching).
     *
     * @var string[]
     */
    private const MAJOR_FLOORS = ['10.0', '11.0', '12.0'];

    /**
     * @return SetInterface[]
     */
    public function provide(): array
    {
        $sets = [];

        foreach (self::DEPRECATION_SETS as $version => $setFilePath) {
            $sets[] = new ComposerTriggeredSet(self::GROUP_NAME, self::PACKAGE_NAME, $version, $setFilePath);
        }

        foreach (self::BREAKING_SETS as $version => $setFilePath) {
            $sets[] = new ComposerTriggeredSet(self::GROUP_NAME.' (breaking)', self::PACKAGE_NAME, $version, $setFilePath);
        }

        foreach (self::MAJOR_FLOORS as $version) {
            $sets[] = new ComposerTriggeredSet(self::GROUP_NAME, self::PACKAGE_NAME, $version, self::BOOTSTRAP_SET);
        }

        return $sets;
    }
}
