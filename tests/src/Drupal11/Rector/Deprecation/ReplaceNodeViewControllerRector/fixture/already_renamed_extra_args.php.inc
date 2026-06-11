<?php

// Simulates the companion RenameClassRector having already rewritten the class
// name: the argument trim must still apply.
$controller = new \Drupal\Core\Entity\Controller\EntityViewController($entityTypeManager, $renderer, $currentUser, $entityRepository);
?>
-----
<?php

// Simulates the companion RenameClassRector having already rewritten the class
// name: the argument trim must still apply.
$controller = new \Drupal\Core\Entity\Controller\EntityViewController($entityTypeManager, $renderer);
?>
