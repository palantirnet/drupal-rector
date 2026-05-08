<?php

// Result used as argument to another function
$url = url_to_string(comment_uri($comment));

?>
-----
<?php

// Result used as argument to another function
$url = url_to_string(\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $comment->permalink(), fn() => comment_uri($comment)));

?>
