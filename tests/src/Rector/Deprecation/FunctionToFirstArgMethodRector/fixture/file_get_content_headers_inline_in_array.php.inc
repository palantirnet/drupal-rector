<?php

// Result used inline in an array
$data = ['headers' => file_get_content_headers($file), 'name' => 'test'];

?>
-----
<?php

// Result used inline in an array
$data = ['headers' => \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $file->getDownloadHeaders(), fn() => file_get_content_headers($file)), 'name' => 'test'];

?>
