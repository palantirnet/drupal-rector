<?php

function full_example($comment, $term) {
    $url = comment_uri($comment);
    $route = taxonomy_term_uri($term);
}

?>
-----
<?php

function full_example($comment, $term) {
    $url = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.3.0', fn() => $comment->permalink(), fn() => comment_uri($comment));
    $route = $term->toUrl();
}

?>
