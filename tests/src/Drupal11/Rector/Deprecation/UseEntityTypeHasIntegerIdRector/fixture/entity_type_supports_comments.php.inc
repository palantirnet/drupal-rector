<?php

use Drupal\comment\CommentTypeForm;

class MyCommentForm extends CommentTypeForm {
    public function doSomething($entity_type): void {
        $a = $this->entityTypeSupportsComments($entity_type);
    }
}

?>
-----
<?php

use Drupal\comment\CommentTypeForm;

class MyCommentForm extends CommentTypeForm {
    public function doSomething($entity_type): void {
        $a = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => $entity_type->hasIntegerId(), fn() => $this->entityTypeSupportsComments($entity_type));
    }
}

?>
