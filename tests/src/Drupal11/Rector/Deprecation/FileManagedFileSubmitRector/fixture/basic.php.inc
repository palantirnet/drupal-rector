<?php

function example() {
    $form['upload']['#submit'] = ['file_managed_file_submit'];
    $form['actions']['#submit'][] = 'file_managed_file_submit';
}
?>
-----
<?php

function example() {
    $form['upload']['#submit'] = [\Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => [\Drupal\file\Element\ManagedFile::class, 'submit'], fn() => 'file_managed_file_submit')];
    $form['actions']['#submit'][] = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => [\Drupal\file\Element\ManagedFile::class, 'submit'], fn() => 'file_managed_file_submit');
}
?>
