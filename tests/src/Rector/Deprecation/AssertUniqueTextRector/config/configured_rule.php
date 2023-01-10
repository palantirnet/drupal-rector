<?php declare(strict_types=1);

use DrupalRector\Rector\Deprecation\AssertNoUniqueTextRector;
use DrupalRector\Rector\Deprecation\AssertUniqueTextRector;
use Rector\Config\RectorConfig;

return static function (RectorConfig $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(AssertUniqueTextRector::class);
    $services->set(AssertNoUniqueTextRector::class);

    $parameters = $containerConfigurator->parameters();
    $parameters->set('drupal_rector_notices_as_comments', true);
};
