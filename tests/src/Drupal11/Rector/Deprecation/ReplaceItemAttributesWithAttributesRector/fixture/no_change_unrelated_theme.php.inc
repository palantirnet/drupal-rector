<?php

// '#item_attributes' on a theme hook other than image_formatter /
// responsive_image_formatter is unrelated and must not be changed.
$element = [
    '#theme' => 'table',
    '#item_attributes' => ['class' => ['my-thing']],
];

// A plain associative array that merely happens to contain an
// '#item_attributes' key but has no '#theme' must not be changed either.
$data = [
    '#item_attributes' => ['class' => ['nope']],
];
