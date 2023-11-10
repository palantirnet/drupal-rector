<?php

function simple_example() {
    drupal_get_filename('module', 'node');
    drupal_get_filename('theme', 'seven');
    drupal_get_filename('profile', 'standard');
    drupal_get_path('module', 'node');
    drupal_get_path('theme', 'seven');
    drupal_get_path('profile', 'standard');

    $filename1 = drupal_get_filename('module', 'node');
    $filename2 = drupal_get_filename('theme', 'seven');
    $filename3 = drupal_get_filename('profile', 'standard');
    $path1 = drupal_get_path('module', 'node');
    $path2 = drupal_get_path('theme', 'seven');
    $path4 = drupal_get_path('profile', 'standard');

    drupal_get_path('unknown_type', 'standard');


    define('BE_SURE_MODULE_PATH', drupal_get_path('module', 'be_sure'));

    define('BE_SURE_MODULE_PATH', drupal_get_path('unknown', 'be_sure'));

    $test = [
        'logo' => file_create_url(drupal_get_path('module', 'dalim_es') . '/images/pdf_logo.svg'),
    ];

    $test = [
        'logo' => file_create_url(drupal_get_path('unkown', 'dalim_es') . '/images/pdf_logo.svg'),
    ];
}
?>
-----
<?php

function simple_example() {
    \Drupal::service('extension.list.module')->getPathname('node');
    \Drupal::service('extension.list.theme')->getPathname('seven');
    \Drupal::service('extension.list.profile')->getPathname('standard');
    \Drupal::service('extension.list.module')->getPath('node');
    \Drupal::service('extension.list.theme')->getPath('seven');
    \Drupal::service('extension.list.profile')->getPath('standard');

    $filename1 = \Drupal::service('extension.list.module')->getPathname('node');
    $filename2 = \Drupal::service('extension.list.theme')->getPathname('seven');
    $filename3 = \Drupal::service('extension.list.profile')->getPathname('standard');
    $path1 = \Drupal::service('extension.list.module')->getPath('node');
    $path2 = \Drupal::service('extension.list.theme')->getPath('seven');
    $path4 = \Drupal::service('extension.list.profile')->getPath('standard');

    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Unsupported extension type encountered, using extension.path.resolver instead of extension.list
    \Drupal::service('extension.path.resolver')->getPath('unknown_type', 'standard');


    define('BE_SURE_MODULE_PATH', \Drupal::service('extension.list.module')->getPath('be_sure'));

    define('BE_SURE_MODULE_PATH', \Drupal::service('extension.path.resolver')->getPath('unknown', 'be_sure'));

    $test = [
        'logo' => file_create_url(\Drupal::service('extension.list.module')->getPath('dalim_es') . '/images/pdf_logo.svg'),
    ];

    $test = [
        'logo' => file_create_url(\Drupal::service('extension.path.resolver')->getPath('unkown', 'dalim_es') . '/images/pdf_logo.svg'),
    ];
}
?>
