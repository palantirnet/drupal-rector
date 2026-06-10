<?php
require __DIR__ . '/vendor/autoload.php';
foreach ((new ReflectionClass(\Rector\NodeTypeResolver\Node\AttributeKey::class))->getConstants() as $k => $v) {
    if (stripos($k, 'parent') !== false || stripos((string) $v, 'parent') !== false) {
        echo "$k = $v\n";
    }
}
