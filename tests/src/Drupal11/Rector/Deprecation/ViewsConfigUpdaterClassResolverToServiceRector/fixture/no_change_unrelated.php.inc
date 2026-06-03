<?php

use Drupal\views\ViewsConfigUpdater;

function unrelated_method() {
    // Different method on \Drupal — must not be transformed.
    \Drupal::service(ViewsConfigUpdater::class);
}

function unrelated_class() {
    // classResolver() with a different class — must not be transformed,
    // because only ViewsConfigUpdater is now a registered service.
    \Drupal::classResolver(\Drupal\Core\Some\OtherClass::class);
}

function unrelated_receiver() {
    // Sibling class with same method name — must not be transformed.
    \Drupal\Core\DependencyInjection\ClassResolver::classResolver(ViewsConfigUpdater::class);
}

function string_argument() {
    // String literal arg, not a class const — must not be transformed,
    // because the digest pattern requires Fqcn::class.
    \Drupal::classResolver('Drupal\views\ViewsConfigUpdater');
}

function wrong_arg_count() {
    // Wrong arity — must not be transformed.
    \Drupal::classResolver();
}
?>
-----
<?php

use Drupal\views\ViewsConfigUpdater;

function unrelated_method() {
    // Different method on \Drupal — must not be transformed.
    \Drupal::service(ViewsConfigUpdater::class);
}

function unrelated_class() {
    // classResolver() with a different class — must not be transformed,
    // because only ViewsConfigUpdater is now a registered service.
    \Drupal::classResolver(\Drupal\Core\Some\OtherClass::class);
}

function unrelated_receiver() {
    // Sibling class with same method name — must not be transformed.
    \Drupal\Core\DependencyInjection\ClassResolver::classResolver(ViewsConfigUpdater::class);
}

function string_argument() {
    // String literal arg, not a class const — must not be transformed,
    // because the digest pattern requires Fqcn::class.
    \Drupal::classResolver('Drupal\views\ViewsConfigUpdater');
}

function wrong_arg_count() {
    // Wrong arity — must not be transformed.
    \Drupal::classResolver();
}
?>
