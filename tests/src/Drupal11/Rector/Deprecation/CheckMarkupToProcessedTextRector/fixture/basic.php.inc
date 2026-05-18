<?php

function example_check_markup($text, $format_id, $langcode, $filter_types_to_skip): void
{
    $output = check_markup($text, $format_id);
    $full = check_markup($text, $format_id, $langcode, $filter_types_to_skip);
    $named = check_markup(text: $text, format_id: $format_id);
}
?>
-----
<?php

function example_check_markup($text, $format_id, $langcode, $filter_types_to_skip): void
{
    $output = ['#type' => 'processed_text', '#text' => $text, '#format' => $format_id];
    $full = ['#type' => 'processed_text', '#text' => $text, '#format' => $format_id, '#langcode' => $langcode, '#filter_types_to_skip' => $filter_types_to_skip];
    $named = ['#type' => 'processed_text', '#text' => $text, '#format' => $format_id];
}
?>
