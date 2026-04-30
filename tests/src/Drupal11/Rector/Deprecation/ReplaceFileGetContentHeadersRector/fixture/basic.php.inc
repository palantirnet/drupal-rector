<?php

$headers = file_get_content_headers($file);
$other = file_get_something_else($file);

?>
-----
<?php

$headers = $file->getDownloadHeaders();
$other = file_get_something_else($file);

?>
