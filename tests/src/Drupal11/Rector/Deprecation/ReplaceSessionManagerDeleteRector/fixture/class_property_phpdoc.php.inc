<?php

namespace Drupal\some_module;

use Drupal\Core\Session\SessionManagerInterface;

class SomeService
{
    /**
     * @var Drupal\Core\Session\SessionManager
     */
    protected $sessionManager;

    public function __construct(SessionManagerInterface $session_manager)
    {
        $this->sessionManager = $session_manager;
    }

    public function deleteUserSessions(int $uid): void
    {
        $this->sessionManager->delete($uid);
    }
}

?>
-----
<?php

namespace Drupal\some_module;

use Drupal\Core\Session\SessionManagerInterface;

class SomeService
{
    /**
     * @var Drupal\Core\Session\SessionManager
     */
    protected $sessionManager;

    public function __construct(SessionManagerInterface $session_manager)
    {
        $this->sessionManager = $session_manager;
    }

    public function deleteUserSessions(int $uid): void
    {
        \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\Core\Session\UserSessionRepositoryInterface::class)->deleteAll($uid), fn() => $this->sessionManager->delete($uid));
    }
}

?>
