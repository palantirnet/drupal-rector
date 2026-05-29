<?php

declare(strict_types=1);

namespace Drupal\Core\Session;

if (interface_exists(\Drupal\Core\Session\SessionManagerInterface::class)) {
    return;
}

interface SessionManagerInterface
{
    public function delete(int $uid): void;
}
