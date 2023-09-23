<?php

function full_example() {
    $vids = taxonomy_vocabulary_get_names();

    $terms = taxonomy_term_load_multiple_by_name(
        'Foo',
        'topics'
    );

    $term = reset($terms);

    $url = taxonomy_term_uri($term);

    taxonomy_terms_static_reset();

    taxonomy_vocabulary_static_reset($vids);

    taxonomy_implode_tags();
    $var = taxonomy_implode_tags();

    $name = taxonomy_term_title($term);

    drupal_static_reset('taxonomy_vocabulary_get_names');
}

?>
-----
<?php

function full_example() {
    $vids = \Drupal::entityQuery('taxonomy_vocabulary')->execute();

    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties(['name' => 'Foo', 'vid' => 'topics']);

    $term = reset($terms);

    $url = $term->toUrl();

    \Drupal::entityTypeManager()->getStorage('taxonomy_term')->resetCache();

    \Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->resetCache($vids);

    \Drupal\Core\Entity\Element\EntityAutocomplete::getEntityLabels();
    $var = \Drupal\Core\Entity\Element\EntityAutocomplete::getEntityLabels();

    $name = $term->label();

    \Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->resetCache();
}

?>
