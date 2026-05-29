<?php

function example(array $element) {
    $hidden = hide($element);
    if (show($element)) {
        return $element;
    }
}
?>
-----
<?php

function example(array $element) {
    $hidden = hide($element);
    if (show($element)) {
        return $element;
    }
}
?>
