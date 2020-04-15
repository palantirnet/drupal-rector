<?php

namespace DrupalRector\Rector\Deprecation;

use Rector\RectorDefinition\CodeSample;
use Rector\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated \Drupal::url() calls.
 *
 * There is no referenced change record for this. This may be related, https://www.drupal.org/node/2046643.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class URLRector extends StaticToServiceBase
{
    protected $deprecatedFullQualifiedClassName = 'Drupal';

    protected $deprecatedMethodName = 'url';

    protected $serviceName = 'url_generator';

    protected $serviceMethodName = 'generateFromRoute';

  /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated \Drupal::url() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
\Drupal::url('user.login');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('url_generator')->generateFromRoute('user.login');
CODE_AFTER
            )
        ]);
    }
}
