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

    $name = taxonomy_term_title($term);

    drupal_static_reset('taxonomy_vocabulary_get_names');

}
