<?php declare(strict_types=1);

namespace DrupalRector\Rector\Deprecation;

use DrupalRector\Rector\Deprecation\Base\AssertLegacyTraitBase;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class BuildXPathQueryRector extends AssertLegacyTraitBase
{

    protected $deprecatedMethodName = 'buildXPathQuery';
    protected $methodName = 'buildXPathQuery';

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Fixes deprecated AssertLegacyTrait::buildXPathQuery() calls', [
            new CodeSample(
                <<<'CODE_BEFORE'
$xpath = $this->buildXPathQuery('//select[@name=:name]', [':name' => $name]);
$fields = $this->xpath($xpath);
CODE_BEFORE
                ,
                <<<'CODE_AFTER'
$xpath = $this->assertSession()->buildXPathQuery('//select[@name=:name]', [':name' => $name]);
$fields = $this->xpath($xpath);
CODE_AFTER
            )
        ]);
    }
}
