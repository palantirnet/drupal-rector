<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\DrupalServiceRenameBase;

/**
 * Renames deprecated Drupal::services('path_processor_alias') argument.
 *
 * See https://www.drupal.org/node/3092086 for change record.
 *
 * What is covered:
 *  - Replacement in Drupal::service() calls.
 */
final class PathProcessorAliasServiceNameRector extends DrupalServiceRenameBase {

  /**
   * The deprecated argument.
   *
   * @var string.
   */
  protected $deprecatedArgument = 'path_processor_alias';

  /**
   * The replacement argument.
   *
   * @var string.
   */
  protected $argument = 'path_alias.path_processor';
}
