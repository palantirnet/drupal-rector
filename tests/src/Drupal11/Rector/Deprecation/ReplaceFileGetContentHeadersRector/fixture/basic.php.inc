<?php

$headers = file_get_content_headers($file);
$other = file_get_something_else($file);

?>
-----
<?php

$headers = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $file->getDownloadHeaders(), fn() => file_get_content_headers($file));
$other = file_get_something_else($file);

?>
