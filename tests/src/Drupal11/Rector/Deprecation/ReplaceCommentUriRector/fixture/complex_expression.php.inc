<?php

// Complex expression as argument — becomes $this->getComment()->permalink()
$url = comment_uri($this->getComment());

?>
-----
<?php

// Complex expression as argument — becomes $this->getComment()->permalink()
$url = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $this->getComment()->permalink(), fn() => comment_uri($this->getComment()));

?>
