<?php

unset($_SESSION['my_key']);
unset($_SESSION['a'], $local, $_SESSION['b'], $other);
unset($local_only);

?>
-----
<?php

\Drupal::request()->getSession()->remove('my_key');
\Drupal::request()->getSession()->remove('a');
unset($local);
\Drupal::request()->getSession()->remove('b');
unset($other);
unset($local_only);

?>
