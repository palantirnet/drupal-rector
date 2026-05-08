<?php

declare(strict_types=1);

namespace Drupal\Core\Session;

if (class_exists(\Drupal\Core\Session\SessionManager::class)) {
    return;
}

class SessionManager implements SessionManagerInterface
{
    public function delete(int $uid): void {}
}
