<?php

function simple_example() {
    $value = OldClass::OLD_CONST;
}
?>
-----
<?php

function simple_example() {
    $value = \NewClass::NEW_CONST;
}
?>
