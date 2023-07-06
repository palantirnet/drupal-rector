<?php

declare(strict_types=1);

namespace DrupalRector\Contract;

use Rector\Core\Contract\Rector\PhpRectorInterface;

interface DrupalCoreRectorInterface extends PhpRectorInterface
{
    public function getVersion(): string;
}
