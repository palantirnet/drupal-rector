<?php

// $file argument is a method call expression
$headers = file_get_content_headers($this->getFile());

?>
-----
<?php

// $file argument is a method call expression
$headers = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.2.0', fn() => $this->getFile()->getDownloadHeaders(), fn() => file_get_content_headers($this->getFile()));

?>
