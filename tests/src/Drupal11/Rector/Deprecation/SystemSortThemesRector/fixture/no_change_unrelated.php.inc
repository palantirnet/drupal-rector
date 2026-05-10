<?php

function example(array $items): void {
    uasort($items, 'some_other_sort_function');
}
?>
-----
<?php

function example(array $items): void {
    uasort($items, 'some_other_sort_function');
}
?>
