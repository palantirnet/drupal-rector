<?php

use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;

$location = CommentItemInterface::FORM_BELOW;
$other = CommentItemInterface::FORM_SEPARATE_PAGE;
?>
-----
<?php

use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;

$location = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\comment\FormLocation::Below, fn() => CommentItemInterface::FORM_BELOW);
$other = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal\comment\FormLocation::SeparatePage, fn() => CommentItemInterface::FORM_SEPARATE_PAGE);
?>
