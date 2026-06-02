<?php

declare(strict_types=1);

namespace Drupal\path_alias;

if (interface_exists(\Drupal\path_alias\AliasManagerInterface::class)) {
    return;
}

interface AliasManagerInterface
{
}
