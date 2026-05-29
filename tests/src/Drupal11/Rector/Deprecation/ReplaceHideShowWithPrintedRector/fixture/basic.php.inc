<?php

function example(array $element, array $other_element) {
    hide($element);
    show($other_element);
}
?>
-----
<?php

function example(array $element, array $other_element) {
    $element['#printed'] = true;
    $other_element['#printed'] = false;
}
?>
