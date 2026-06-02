<?php

function example_render_array() {
    $build = ['#markup' => 'foo', '#access' => 1];
    $other = ['#markup' => 'bar', '#access' => 0];
    return [$build, $other];
}
?>
-----
<?php

function example_render_array() {
    $build = ['#markup' => 'foo', '#access' => true];
    $other = ['#markup' => 'bar', '#access' => false];
    return [$build, $other];
}
?>
