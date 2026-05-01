<?php

// Complex expression as argument — becomes $this->getComment()->permalink()
$url = comment_uri($this->getComment());

?>
-----
<?php

// Complex expression as argument — becomes $this->getComment()->permalink()
$url = $this->getComment()->permalink();

?>
