<?php

/** @var \Drupal\comment\CommentManagerInterface $comment_manager */
$count = $comment_manager->getCountNewComments($entity, 'comment', 0);
?>
-----
<?php

/** @var \Drupal\comment\CommentManagerInterface $comment_manager */
$count = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\history\HistoryManager::class)->getCountNewComments($entity, 'comment', 0), fn() => $comment_manager->getCountNewComments($entity, 'comment', 0));
?>
