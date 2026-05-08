<?php

/** @var \Drupal\Core\Session\SessionManagerInterface $sessionManager */
$sessionManager->delete($uid);

?>
-----
<?php

/** @var \Drupal\Core\Session\SessionManagerInterface $sessionManager */
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service(\Drupal\Core\Session\UserSessionRepositoryInterface::class)->deleteAll($uid), fn() => $sessionManager->delete($uid));

?>
