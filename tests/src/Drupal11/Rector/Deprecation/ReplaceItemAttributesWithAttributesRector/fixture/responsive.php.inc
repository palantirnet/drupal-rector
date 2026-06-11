<?php

$build['image'] = [
    '#theme' => 'responsive_image_formatter',
    '#item' => $item,
    '#item_attributes' => ['class' => ['responsive-image']],
];

?>
-----
<?php

$build['image'] = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => [
    '#theme' => 'responsive_image_formatter',
    '#item' => $item,
    '#attributes' => ['class' => ['responsive-image']],
], fn() => [
    '#theme' => 'responsive_image_formatter',
    '#item' => $item,
    '#item_attributes' => ['class' => ['responsive-image']],
]);

?>
