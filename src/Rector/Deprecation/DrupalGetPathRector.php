<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\ExtensionPathBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class DrupalGetPathRector extends ExtensionPathBase
{
    protected $functionName = 'drupal_get_path';

    protected $methodName = 'getPath';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated drupal_get_path() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
drupal_get_path('module', 'node');
drupal_get_path('theme', 'seven');
drupal_get_path('profile', 'standard');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
\Drupal::service('extension.list.module')->getPath('node');
\Drupal::service('extension.list.theme')->getPath('seven');
\Drupal::service('extension.list.profile')->getPath('standard');
CODE_AFTER
            )
        ]);
    }
}
