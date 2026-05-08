<?php

use Drupal\Core\Session\SessionManagerInterface;

class SomeService
{
    public function __construct(
        protected SessionManagerInterface $sessionManager,
    ) {}

    public function deleteUserSessions(int $uid): void
    {
        $this->sessionManager->delete($uid);
    }
}

?>
-----
<?php

use Drupal\Core\Session\SessionManagerInterface;

class SomeService
{
    public function __construct(
        protected SessionManagerInterface $sessionManager,
    ) {}

    public function deleteUserSessions(int $uid): void
    {
        \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\Core\Session\UserSessionRepositoryInterface::class)->deleteAll($uid), fn() => $this->sessionManager->delete($uid));
    }
}

?>
