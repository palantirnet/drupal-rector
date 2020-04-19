<?php

namespace DrupalRector\Rector\Deprecation;

use Rector\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated \Drupal::l() calls.
 *
 * See https://www.drupal.org/node/2346779 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class DrupalLRector extends StaticToServiceBase
{
    protected $deprecatedFullyQualifiedClassName = 'Drupal';

    protected $deprecatedMethodName = 'l';

    protected $serviceName = 'link_generator';

    protected $serviceMethodName = 'generate';

    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated \Drupal::l() calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
\Drupal::l('User Login', \Drupal::service('url_generator')->generateFromRoute('user.login'));
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('link_generator')->generate('User Login', \Drupal::service('url_generator')->generateFromRoute('user.login'));
CODE_AFTER
            )
        ]);
    }
}
