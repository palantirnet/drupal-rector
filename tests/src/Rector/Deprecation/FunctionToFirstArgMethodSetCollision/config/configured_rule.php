<?php

declare(strict_types=1);

use DrupalRector\Drupal9\Rector\Deprecation\FunctionToFirstArgMethodRector as Drupal9FunctionToFirstArgMethodRector;
use DrupalRector\Drupal9\Rector\ValueObject\FunctionToFirstArgMethodConfiguration as Drupal9FunctionToFirstArgMethodConfiguration;
use DrupalRector\Rector\Deprecation\FunctionToFirstArgMethodRector;
use DrupalRector\Rector\ValueObject\FunctionToFirstArgMethodConfiguration;
use DrupalRector\Tests\Rector\Deprecation\DeprecationBase;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    // The generic rule, as registered by the Drupal 11 sets.
    DeprecationBase::addClass(FunctionToFirstArgMethodRector::class, $rectorConfig, false, [
        new FunctionToFirstArgMethodConfiguration('11.3.0', 'comment_uri', 'permalink'),
    ]);

    // The Drupal 9 subclass, as registered by the Drupal 9 set. Because it extends
    // the generic rule, Rector's container delivers the generic configuration above
    // to this instance as well; the subclass must ignore it rather than throw.
    DeprecationBase::addClass(Drupal9FunctionToFirstArgMethodRector::class, $rectorConfig, false, [
        new Drupal9FunctionToFirstArgMethodConfiguration('taxonomy_term_uri', 'toUrl'),
    ]);
};
