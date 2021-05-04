<?php

namespace DrupalRector\Twig\Transformer;

use Rector\Core\Provider\CurrentFileProvider;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class TwigReplaceTransformer implements TwigTransformer
{

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Convert replace function into array', [
            new CodeSample(
                <<<'CODE_SAMPLE'
{% set themeclass = item.title|replace(' ', '-') %}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
{% set themeclass = item.title|replace({' ': '-'}) %}
CODE_SAMPLE
            ),
        ]);
    }

    public function transform(string $twigContent): string
    {
        $twigContent = preg_replace('/replace\([\'"]{1}(.*)[\'"]{1},\s*[\'"]{1}(.*)[\'"]{1}\)/i', 'replace({\'$1\': \'$2\'})', $twigContent);
        return $twigContent;
    }
}
