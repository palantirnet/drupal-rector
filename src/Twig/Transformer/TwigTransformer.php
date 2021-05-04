<?php

namespace DrupalRector\Twig\Transformer;

use Rector\Core\Contract\Rector\RectorInterface;
use Rector\Core\Provider\CurrentFileProvider;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

interface TwigTransformer extends RectorInterface
{
    public function transform(string $twigContent): string;
}
