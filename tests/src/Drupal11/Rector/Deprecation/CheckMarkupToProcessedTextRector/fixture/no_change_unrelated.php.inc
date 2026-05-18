<?php

function example_no_change($text, $format_id): void
{
    // Calls to other functions are not affected.
    $output = check_plain($text);
    $other = some_other_markup($text, $format_id);
}
?>
-----
<?php

function example_no_change($text, $format_id): void
{
    // Calls to other functions are not affected.
    $output = check_plain($text);
    $other = some_other_markup($text, $format_id);
}
?>
