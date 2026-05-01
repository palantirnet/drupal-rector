<?php

// Result used inline in an array
$data = ['headers' => file_get_content_headers($file), 'name' => 'test'];

?>
-----
<?php

// Result used inline in an array
$data = ['headers' => $file->getDownloadHeaders(), 'name' => 'test'];

?>
