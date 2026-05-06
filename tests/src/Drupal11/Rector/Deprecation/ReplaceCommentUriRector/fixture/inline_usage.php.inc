<?php

// Result used inline (not assigned)
print comment_uri($comment);

?>
-----
<?php

// Result used inline (not assigned)
print \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $comment->permalink(), fn() => comment_uri($comment));

?>
