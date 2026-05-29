<?php

/** @var \Drupal\node\NodeStorageInterface $storage */
foreach ($storage->revisionIds($node) as $vid) {
    // process revision
}

?>
-----
<?php

/** @var \Drupal\node\NodeStorageInterface $storage */
foreach (array_keys($storage->getQuery()->allRevisions()->condition('nid', $node->id())->accessCheck(FALSE)->execute()) as $vid) {
    // process revision
}

?>
