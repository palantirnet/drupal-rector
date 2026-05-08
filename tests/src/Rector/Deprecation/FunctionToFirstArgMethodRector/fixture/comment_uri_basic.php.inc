<?php

$url = comment_uri($comment);

?>
-----
<?php

$url = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $comment->permalink(), fn() => comment_uri($comment));

?>
