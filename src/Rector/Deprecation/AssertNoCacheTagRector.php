<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class AssertNoCacheTagRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'assertNoCacheTag';
    protected $methodName = 'responseHeaderNotContains';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated \Drupal\FunctionalTests\AssertLegacyTrait::assertNoCacheTag() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$this->assertNoCacheTag('some-cache-tag');
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$this->assertSession()->responseHeaderNotContains('some-cache-tag');
CODE_AFTER
            )
        ]);
    }

}
