<?php

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\EntityLoadBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Replaced deprecated user_load() calls.
 *
 * See https://www.drupal.org/node/2266845 for change record.
 *
 * What is covered:
 * - See EntityLoadBase.php
 *
 * Improvement opportunities
 * - See EntityLoadBase.php
 */
final class UserLoadRector extends EntityLoadBase
{
    protected $entityType = 'user';

    /**
     * @inheritdoc
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated user_load() use',[
            new CodeSample(
                <<<'CODE_BEFORE'
$user = user_load(123);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$user = \Drupal::entityManager()->getStorage('user')->load(123);
CODE_AFTER
            )
        ]);
    }

}
