<?php

namespace DrupalRector\Rector\Deprecation;

use PhpParser\Node;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Renames deprecated Drupal::services('path.alias_manager') argument.
 *
 * See https://www.drupal.org/node/3092086 for change record.
 *
 * What is covered:
 *  - Replacement in Drupal::service() calls.
 */
final class PathAliasManagerServiceNameRector extends DrupalServiceRenameBase {

  /**
   * The deprecated argument.
   *
   * @var string.
   */
  protected $deprecatedArgument = 'path.alias_manager';

  /**
   * The replacement argument.
   *
   * @var string.
   */
  protected $argument = 'path_alias.manager';
}
