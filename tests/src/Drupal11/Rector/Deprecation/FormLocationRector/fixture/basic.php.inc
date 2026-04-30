<?php

use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;

$location = CommentItemInterface::FORM_BELOW;
$other = CommentItemInterface::FORM_SEPARATE_PAGE;
?>
-----
<?php

use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;

$location = \Drupal\comment\FormLocation::Below;
$other = \Drupal\comment\FormLocation::SeparatePage;
?>
