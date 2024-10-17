<?php

declare(strict_types=1);

namespace DrupalRector\Set;

use Rector\Set\Contract\SetListInterface;

final class HookConvertSetList implements SetListInterface
{
    public const HOOK_CONVERT = __DIR__.'/../../config/hook-convert/hook-convert.php';
}
