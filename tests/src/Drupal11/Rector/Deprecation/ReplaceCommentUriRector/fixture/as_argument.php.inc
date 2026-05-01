<?php

// Result used as argument to another function
$url = url_to_string(comment_uri($comment));

?>
-----
<?php

// Result used as argument to another function
$url = url_to_string($comment->permalink());

?>
