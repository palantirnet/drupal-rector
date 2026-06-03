<?php

declare(strict_types=1);

namespace Drupal\path_alias;

if (class_exists(\Drupal\path_alias\AliasManager::class)) {
    return;
}

class AliasManager implements AliasManagerInterface
{
}
