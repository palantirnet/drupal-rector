<?php

declare(strict_types=1);

namespace DrupalRector\Drupal9\Rector\ValueObject;

use DrupalRector\Rector\ValueObject\FunctionToFirstArgMethodConfiguration as GenericFunctionToFirstArgMethodConfiguration;

class FunctionToFirstArgMethodConfiguration extends GenericFunctionToFirstArgMethodConfiguration
{
    public function __construct(string $deprecatedFunctionName, string $methodName)
    {
        parent::__construct('', $deprecatedFunctionName, $methodName);
    }
}
