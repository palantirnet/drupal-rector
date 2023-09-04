<?php

declare(strict_types=1);

namespace DrupalRector\Contract;

interface DrupalCoreRectorInterface
{
    public function getVersion(): string;
}
