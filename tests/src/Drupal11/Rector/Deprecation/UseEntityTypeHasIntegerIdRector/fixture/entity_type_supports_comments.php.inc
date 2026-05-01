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
        $a = $entity_type->hasIntegerId();
    }
}

?>
