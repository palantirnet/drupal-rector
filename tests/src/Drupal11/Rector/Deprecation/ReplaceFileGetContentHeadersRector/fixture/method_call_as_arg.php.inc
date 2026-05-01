<?php

// $file argument is a method call expression
$headers = file_get_content_headers($this->getFile());

?>
-----
<?php

// $file argument is a method call expression
$headers = $this->getFile()->getDownloadHeaders();

?>
