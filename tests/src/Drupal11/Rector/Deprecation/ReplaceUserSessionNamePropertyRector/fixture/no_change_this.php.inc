<?php

use Drupal\Core\Session\UserSession;

class MySession extends UserSession
{
    public function getAccountName(): string
    {
        // $this->name inside the class is not transformed — would cause
        // infinite recursion if rewritten to $this->getAccountName().
        return $this->name;
    }
}

?>
-----
<?php

use Drupal\Core\Session\UserSession;

class MySession extends UserSession
{
    public function getAccountName(): string
    {
        // $this->name inside the class is not transformed — would cause
        // infinite recursion if rewritten to $this->getAccountName().
        return $this->name;
    }
}

?>
