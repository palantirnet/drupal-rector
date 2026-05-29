<?php

/** @var \Drupal\Core\Session\SessionManager $session_manager */
$session_manager->delete($uid);
?>
-----
<?php

/** @var \Drupal\Core\Session\SessionManager $session_manager */
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\Core\Session\UserSessionRepositoryInterface::class)->deleteAll($uid), fn() => $session_manager->delete($uid));
?>
