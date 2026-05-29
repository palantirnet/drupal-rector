<?php

function example($body, $format, $size) {
    $summary = text_summary($body, $format, $size);
}
?>
-----
<?php

function example($body, $format, $size) {
    $summary = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\text\TextSummary')->generate($body, $format, $size), fn() => text_summary($body, $format, $size));
}
?>
