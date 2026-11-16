<?php

function my_module_casts($value) {
  $a = (boolean) $value;
  $b = (integer) $value;
  $c = (double) $value;
  $d = (binary) $value;
  $e = (bool) $value;
  $f = (int) $value;
  $g = (float) $value;
  $h = (string) $value;
  return [$a, $b, $c, $d, $e, $f, $g, $h];
}

?>
-----
<?php

function my_module_casts($value) {
  $a = (bool) $value;
  $b = (int) $value;
  $c = (float) $value;
  $d = (string) $value;
  $e = (bool) $value;
  $f = (int) $value;
  $g = (float) $value;
  $h = (string) $value;
  return [$a, $b, $c, $d, $e, $f, $g, $h];
}

?>
