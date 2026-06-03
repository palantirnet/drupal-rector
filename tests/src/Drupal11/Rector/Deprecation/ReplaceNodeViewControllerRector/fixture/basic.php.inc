<?php

$controller = new \Drupal\node\Controller\NodeViewController($entityTypeManager, $renderer, $currentUser, $entityRepository);
?>
-----
<?php

$controller = new \Drupal\Core\Entity\Controller\EntityViewController($entityTypeManager, $renderer);
?>
