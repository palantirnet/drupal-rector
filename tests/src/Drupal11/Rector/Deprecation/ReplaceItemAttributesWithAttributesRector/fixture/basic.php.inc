<?php

$element = [
    '#theme' => 'image_formatter',
    '#item' => $item,
    '#item_attributes' => ['class' => ['my-image']],
    '#url' => $url,
];

?>
-----
<?php

$element = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => [
    '#theme' => 'image_formatter',
    '#item' => $item,
    '#attributes' => ['class' => ['my-image']],
    '#url' => $url,
], fn() => [
    '#theme' => 'image_formatter',
    '#item' => $item,
    '#item_attributes' => ['class' => ['my-image']],
    '#url' => $url,
]);

?>
