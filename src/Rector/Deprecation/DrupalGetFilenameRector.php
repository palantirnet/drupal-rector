<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\ExtensionPathBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class DrupalGetFilenameRector extends ExtensionPathBase
{
    protected $functionName = 'drupal_get_filename';

    protected $methodName = 'getPathname';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated drupal_get_filename() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
drupal_get_filename('module', 'node');
drupal_get_filename('theme', 'seven');
drupal_get_filename('profile', 'standard');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('extension.list.module')->getPathname('node');
\Drupal::service('extension.list.theme')->getPathname('seven');
\Drupal::service('extension.list.profile')->getPathname('standard');
CODE_AFTER
            )
        ]);
    }
}
