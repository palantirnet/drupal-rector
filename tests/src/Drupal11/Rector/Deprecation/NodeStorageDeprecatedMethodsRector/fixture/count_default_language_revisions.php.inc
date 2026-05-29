<?php

/** @var \Drupal\node\NodeStorageInterface $storage */
$ids = $storage->revisionIds($node);
$storage->countDefaultLanguageRevisions($node);

?>
-----
<?php

/** @var \Drupal\node\NodeStorageInterface $storage */
$ids = array_keys($storage->getQuery()->allRevisions()->condition('nid', $node->id())->accessCheck(FALSE)->execute());

?>
