<?php

// Result used inline (not assigned)
print comment_uri($comment);

?>
-----
<?php

// Result used inline (not assigned)
print $comment->permalink();

?>
