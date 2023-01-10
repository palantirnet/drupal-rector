<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertEqualRector;
use DrupalRector\Rector\Deprecation\AssertNotEqualRector;
use DrupalRector\Rector\Deprecation\AssertRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(AssertRector::class);

    $parameters = $containerConfigurator->parameters();
    $parameters->set('drupal_rector_notices_as_comments', true);
};
