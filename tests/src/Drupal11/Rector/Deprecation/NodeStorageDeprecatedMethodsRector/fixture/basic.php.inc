<?php

/** @var \Drupal\node\NodeStorageInterface $storage */
$ids = $storage->revisionIds($node);
$revisions = $storage->userRevisionIds($account);
?>
-----
<?php

/** @var \Drupal\node\NodeStorageInterface $storage */
$ids = array_keys($storage->getQuery()->allRevisions()->condition('nid', $node->id())->accessCheck(FALSE)->execute());
$revisions = array_keys($storage->getQuery()->allRevisions()->accessCheck(FALSE)->condition('uid', $account->id())->execute());
?>
