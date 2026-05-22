<?php

function example($contextual_links, $id) {
    $result = _contextual_links_to_id($contextual_links);
    $links = _contextual_id_to_links($id);
}
?>
-----
<?php

function example($contextual_links, $id) {
    $result = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\contextual\ContextualLinksSerializer')->linksToId($contextual_links), fn() => _contextual_links_to_id($contextual_links));
    $links = \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '11.4.0', fn() => \Drupal::service('Drupal\contextual\ContextualLinksSerializer')->idToLinks($id), fn() => _contextual_id_to_links($id));
}
?>
