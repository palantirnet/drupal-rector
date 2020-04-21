<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\DrupalServiceRenameBase;

/**
 * Renames deprecated Drupal::services('path.alias_repository') argument.
 *
 * See https://www.drupal.org/node/3092086 for change record.
 *
 * What is covered:
 *  - Replacement in Drupal::service() calls.
 */
final class PathAliasRepositoryRector extends DrupalServiceRenameBase {

  /**
   * The deprecated argument.
   *
   * @var string.
   */
  protected $deprecatedArgument = 'path.alias_repository';

  /**
   * The replacement argument.
   *
   * @var string.
   */
  protected $argument = 'path_alias.repository';
}
