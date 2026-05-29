<?php

// Service retrieval via \Drupal::service() returns mixed, so the type guard
// does not fire and the call is not rewritten.
$count = \Drupal::service('comment.manager')->getCountNewComments($entity);
