<?php

use Drupal\comment\CommentManagerInterface;

class MyCommentService {
    public function __construct(
        protected CommentManagerInterface $commentManager,
    ) {}

    public function countNew(\Drupal\Core\Entity\EntityInterface $entity): int|false {
        return $this->commentManager->getCountNewComments($entity);
    }
}
?>
-----
<?php

use Drupal\comment\CommentManagerInterface;

class MyCommentService {
    public function __construct(
        protected CommentManagerInterface $commentManager,
    ) {}

    public function countNew(\Drupal\Core\Entity\EntityInterface $entity): int|false {
        return \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\history\HistoryManager::class)->getCountNewComments($entity), fn() => $this->commentManager->getCountNewComments($entity));
    }
}
?>
