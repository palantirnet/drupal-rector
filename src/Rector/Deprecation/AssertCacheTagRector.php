<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\AbstractCodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertCacheTagRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertCacheTag';
    protected $methodName = 'responseHeaderContains';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal\FunctionalTests\AssertLegacyTrait::assertCacheTag() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertCacheTag('some-cache-tag');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->responseHeaderContains('some-cache-tag');
CODE_AFTER
            )
        ]);
    }

}
