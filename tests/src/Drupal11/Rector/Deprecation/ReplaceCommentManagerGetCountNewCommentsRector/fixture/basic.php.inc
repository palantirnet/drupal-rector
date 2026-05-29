<?php

/** @var \Drupal\comment\CommentManagerInterface $comment_manager */
$comment_manager->getCountNewComments($entity);
?>
-----
<?php

/** @var \Drupal\comment\CommentManagerInterface $comment_manager */
\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\history\HistoryManager::class)->getCountNewComments($entity), fn() => $comment_manager->getCountNewComments($entity));
?>
