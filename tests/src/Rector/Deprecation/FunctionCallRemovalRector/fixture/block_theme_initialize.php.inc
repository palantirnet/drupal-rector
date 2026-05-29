<?php

function example($theme) {
    block_theme_initialize($theme);
    other_function($theme);
}
?>
-----
<?php

function example($theme) {
    other_function($theme);
}
?>
