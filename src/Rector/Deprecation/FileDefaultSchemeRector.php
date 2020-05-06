<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\FunctionToImmutableConfigBase;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

/**
 * Replaces the deprecated file_default_scheme function calls.
 *
 * @see https://www.drupal.org/node/3049030 for change record.
 */
final class FileDefaultSchemeRector extends FunctionToImmutableConfigBase
{
    protected $deprecatedFunctionName = 'file_default_scheme';

    protected $configObject = 'system.file';

    protected $configName = 'default_scheme';

    /**
     * @inheritDoc
     */
    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition('Fixes deprecated file_default_scheme calls',[
            new CodeSample(
                <<<'CODE_BEFORE'
$file_default_scheme = file_default_scheme();
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$file_default_scheme = \Drupal::config('system.file')->get('default_scheme');
CODE_AFTER
            )
        ]);
    }


}
