<?php

function example(array $element) {
    hide($element['child']);
    show($element['child']['grandchild']);
}
?>
-----
<?php

function example(array $element) {
    $element['child']['#printed'] = true;
    $element['child']['grandchild']['#printed'] = false;
}
?>
