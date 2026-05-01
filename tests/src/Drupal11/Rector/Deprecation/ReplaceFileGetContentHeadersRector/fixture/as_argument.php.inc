<?php

// Result used as argument to another function
send_headers(file_get_content_headers($file));

?>
-----
<?php

// Result used as argument to another function
send_headers($file->getDownloadHeaders());

?>
