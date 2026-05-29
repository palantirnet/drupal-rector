<?php

function example($node) {
    taxonomy_build_node_index($node);
    taxonomy_delete_node_index($node);
    other_function($node);
}
?>
-----
<?php

function example($node) {
    other_function($node);
}
?>
