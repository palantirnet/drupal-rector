<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToServiceBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces deprecated format_date() calls.
 *
 * See https://www.drupal.org/node/1876852 for change record.
 *
 * What is covered:
 * - Static replacement
 *
 * Improvement opportunities
 * - Dependency injection
 */
final class FormatDateRector extends FunctionToServiceBase
{
    protected $deprecatedFunctionName = 'format_date';

    protected $serviceName = 'date.formatter';

    protected $serviceMethodName = 'format';


    /**
     * @inheritdoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated format_date() calls',[
            new CodeSample(
              <<<'CODE_BEFORE'
$date = format_date($timestamp, $type, $format, $timezone, $langcode);
CODE_BEFORE
              ,
              <<<'CODE_AFTER'
$date = \Drupal::service('date.formatter')->format($timestamp, $type, $format, $timezone, $langcode);
CODE_AFTER
            )
        ]);
    }
}
