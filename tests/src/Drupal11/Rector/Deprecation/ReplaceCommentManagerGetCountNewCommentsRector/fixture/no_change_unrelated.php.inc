<?php

// getCountNewComments() on an unrelated class — must not be changed.

class UnrelatedManager {
    public function getCountNewComments(\Drupal\Core\Entity\EntityInterface $entity): int {
        return 0;
    }
}

/** @var \UnrelatedManager $mgr */
$count = $mgr->getCountNewComments($entity);
