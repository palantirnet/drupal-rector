<?php
require __DIR__ . '/vendor/autoload.php';
$consts = (new ReflectionClass(\Rector\NodeTypeResolver\Node\AttributeKey::class))->getConstants();
foreach ($consts as $k => $v) {
    if (preg_match('/ISSET|UNSET|ASSIGN/i', $k)) echo "$k = $v\n";
}
echo "Isset_ is Expr? " . (is_subclass_of(\PhpParser\Node\Expr\Isset_::class, \PhpParser\Node\Expr::class) ? 'yes' : 'no') . "\n";
echo "Unset_ is Stmt? " . (is_subclass_of(\PhpParser\Node\Stmt\Unset_::class, \PhpParser\Node\Stmt::class) ? 'yes' : 'no') . "\n";
