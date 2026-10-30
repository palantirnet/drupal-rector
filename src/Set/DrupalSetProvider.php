<?php

declare(strict_types=1);

namespace DrupalRector\Set;

/**
 * Empty placeholder for the removed composer-based set provider.
 *
 * Rector 2.8 deleted \Rector\Set\Contract\SetProviderInterface, and since Rector
 * 2.6.5 `withSetProviders()` ignores its arguments while
 * `withComposerBased(drupal: true)` loads DrupalSetList::COMPOSER_BASED
 * directly. This class only keeps existing
 * `->withSetProviders(DrupalSetProvider::class)` configs from failing static
 * analysis (e.g. upgrade_status scanning a module's own rector.php).
 *
 * @deprecated since 1.2.0, drop `->withSetProviders(DrupalSetProvider::class)`
 *   and keep `->withComposerBased(drupal: true)`. Will be removed in 2.0.0.
 */
final class DrupalSetProvider
{
}
