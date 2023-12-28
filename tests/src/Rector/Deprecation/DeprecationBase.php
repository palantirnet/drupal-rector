<?php

declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Deprecation;

use DrupalRector\Services\AddCommentService;
use Rector\Config\RectorConfig;

/**
 * Implements a common test pattern for consistency.
 */
class DeprecationBase
{
    /**
     * Adds a class to a test.
     *
     * @param string       $rectorClass   The class being tested
     * @param RectorConfig $rectorConfig  The Rector Config handler
     * @param bool         $add_config    Indicates that config should be added to the test
     * @param array        $configuration Configuration for the configured rule
     */
    public static function addClass(string $rectorClass, RectorConfig $rectorConfig, bool $add_notice_config = true, array $configuration = [])
    {
        if ($add_notice_config) {
            $rectorConfig->singleton(AddCommentService::class, function () {
                return new AddCommentService(true);
            });
        }

        if (count($configuration) > 0) {
            $rectorConfig->ruleWithConfiguration($rectorClass, $configuration);
        } else {
            $rectorConfig->rule($rectorClass);
        }
    }
}
