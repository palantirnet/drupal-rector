<?php

declare(strict_types=1);

use DrupalRector\Drupal11\Rector\Deprecation\NodeStorageDeprecatedMethodsRector;
use DrupalRector\Drupal11\Rector\Deprecation\ReplaceCommentManagerGetCountNewCommentsRector;
use DrupalRector\Rector\Deprecation\FunctionToServiceRector;
use DrupalRector\Rector\ValueObject\DrupalIntroducedVersionConfiguration;
use DrupalRector\Rector\ValueObject\FunctionToServiceConfiguration;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // https://www.drupal.org/node/3543035
    // CommentManagerInterface::getCountNewComments() deprecated in drupal:11.3.0, removed in drupal:12.0.0.
    // Replaced by \Drupal\history\HistoryManager::getCountNewComments().
    $rectorConfig->ruleWithConfiguration(ReplaceCommentManagerGetCountNewCommentsRector::class, [
        new DrupalIntroducedVersionConfiguration('11.3.0'),
    ]);

    // https://www.drupal.org/node/3396062
    // NodeStorage::revisionIds() and userRevisionIds() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by equivalent entity queries.
    $rectorConfig->rule(NodeStorageDeprecatedMethodsRector::class);

    // https://www.drupal.org/node/3571623
    // node_mass_update() deprecated in drupal:11.3.0, removed in drupal:13.0.0.
    // Replaced by \Drupal\node\NodeBulkUpdate::process().
    $rectorConfig->ruleWithConfiguration(FunctionToServiceRector::class, [
        new FunctionToServiceConfiguration('11.3.0', 'node_mass_update', 'Drupal\node\NodeBulkUpdate', 'process'),
    ]);
};
