<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\DrupalServiceRenameBase;

/**
 * Renames deprecated Drupal::services('path.alias_whitelist') argument.
 *
 * See https://www.drupal.org/node/3092086 for change record.
 *
 * What is covered:
 *  - Replacement in Drupal::service() calls.
 */
final class PathAliasWhitelistServiceNameRector extends DrupalServiceRenameBase {

  /**
   * The deprecated argument.
   *
   * @var string.
   */
  protected $deprecatedArgument = 'path.alias_whitelist';

  /**
   * The replacement argument.
   *
   * @var string.
   */
  protected $argument = 'path_alias.whitelist';
}
