<?php declare(strict_types=1);

namespace DrupalRector\Tests\Rector\Deprecation;

use DrupalRector\Rector\Visitor\CommentingVisitor;
use Rector\Config\RectorConfig;
use Rector\NodeTypeResolver\PHPStan\Scope\Contract\NodeVisitor\ScopeResolverNodeVisitorInterface;

/**
 * Implements a common test pattern for consistency.
 */
class DeprecationBase {

    /**
     * Adds a class to a test.
     *
     * @param string $rectorClass
     *   The class being tested.
     * @param \Rector\Config\RectorConfig $rectorConfig
     *   The Rector Config handler.
     * @param bool $add_config
     *   Indicates that config should be added to the test.
     */
    public static function addClass(string $rectorClass, RectorConfig $rectorConfig, bool $add_config = TRUE) {
        if ($add_config) {
            $rectorConfig->ruleWithConfiguration($rectorClass, [
                'drupal_rector_notices_as_comments' => '%drupal_rector_notices_as_comments%',
            ]);
            $rectorConfig->services()->set(CommentingVisitor::class)->tag(ScopeResolverNodeVisitorInterface::class);
        }
        else {
            $rectorConfig->rule($rectorClass);
        }

        self::addParameters($rectorConfig);
    }

    /**
     * Ensures configuration options are present.
     *
     * @param \Rector\Config\RectorConfig $rectorConfig
     *   The Rector Config handler.
     */
    public static function addParameters(RectorConfig $rectorConfig) {
        $parameters = $rectorConfig->parameters();
        $parameters->set('drupal_rector_notices_as_comments', true);
    }

}
