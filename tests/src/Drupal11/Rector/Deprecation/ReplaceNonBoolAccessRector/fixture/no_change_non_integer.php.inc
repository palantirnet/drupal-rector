<?php

function example_no_change_non_integer($access_result, $variable) {
    // Already boolean — leave alone.
    $a = ['#markup' => 'foo', '#access' => true];
    $b = ['#markup' => 'foo', '#access' => false];

    // Variable — cannot determine boolean replacement statically.
    $c = ['#markup' => 'foo', '#access' => $variable];

    // AccessResultInterface expression — leave alone.
    $d = ['#markup' => 'foo', '#access' => $access_result];

    // Unrelated key — even integer values must be ignored.
    $e = ['#weight' => 1, '#markup' => 'foo'];

    return [$a, $b, $c, $d, $e];
}
