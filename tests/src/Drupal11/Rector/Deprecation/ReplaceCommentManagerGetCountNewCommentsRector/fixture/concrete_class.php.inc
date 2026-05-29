<?php

// Receiver typed as the concrete CommentManager class (not the interface).
// Without a full PHPStan class-hierarchy analysis, isObjectType() only sees the
// declared type, so this is treated as a no-change case.
use Drupal\comment\CommentManager;

/** @var \Drupal\comment\CommentManager $comment_manager */
$count = $comment_manager->getCountNewComments($entity);
