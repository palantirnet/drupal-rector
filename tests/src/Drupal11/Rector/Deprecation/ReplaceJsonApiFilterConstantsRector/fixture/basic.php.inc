<?php

return [
    JSONAPI_FILTER_AMONG_ALL => 'a',
    JSONAPI_FILTER_AMONG_PUBLISHED => 'b',
    JSONAPI_FILTER_AMONG_ENABLED => 'c',
    JSONAPI_FILTER_AMONG_OWN => 'd',
];

?>
-----
<?php

return [
    \Drupal\jsonapi\JsonApiFilter::AMONG_ALL => 'a',
    \Drupal\jsonapi\JsonApiFilter::AMONG_PUBLISHED => 'b',
    \Drupal\jsonapi\JsonApiFilter::AMONG_ENABLED => 'c',
    \Drupal\jsonapi\JsonApiFilter::AMONG_OWN => 'd',
];

?>
