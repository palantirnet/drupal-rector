<?php

use Drupal\comment\CommentManagerInterface;

// Traditional @var annotation + constructor assignment (forum module pattern).
class ForumManager {
    /**
     * @var \Drupal\comment\CommentManagerInterface
     */
    protected $commentManager;

    public function __construct(CommentManagerInterface $commentManager) {
        $this->commentManager = $commentManager;
    }

    public function getCountNewComments(\Drupal\Core\Entity\EntityInterface $entity): int|false {
        return $this->commentManager->getCountNewComments($entity, 'comment_forum', 0);
    }
}
?>
-----
<?php

use Drupal\comment\CommentManagerInterface;

// Traditional @var annotation + constructor assignment (forum module pattern).
class ForumManager {
    /**
     * @var \Drupal\comment\CommentManagerInterface
     */
    protected $commentManager;

    public function __construct(CommentManagerInterface $commentManager) {
        $this->commentManager = $commentManager;
    }

    public function getCountNewComments(\Drupal\Core\Entity\EntityInterface $entity): int|false {
        return \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => \Drupal::service(\Drupal\history\HistoryManager::class)->getCountNewComments($entity, 'comment_forum', 0), fn() => $this->commentManager->getCountNewComments($entity, 'comment_forum', 0));
    }
}
?>
