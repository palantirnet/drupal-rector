<?php

// Already migrated to '#attributes': nothing to do, and it must not be
// re-wrapped in a DeprecationHelper call.
$element = [
    '#theme' => 'image_formatter',
    '#item' => $item,
    '#attributes' => ['class' => ['my-image']],
];
