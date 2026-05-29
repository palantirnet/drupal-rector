<?php

// PDO::FETCH_* outside a recognised method call or 'fetch' array key is not transformed.
$mode = \PDO::FETCH_ASSOC;
$ternary = $useAssoc ? \PDO::FETCH_ASSOC : \PDO::FETCH_OBJ;

function example($mode = \PDO::FETCH_ASSOC) {
    return $mode;
}

?>
-----
<?php

// PDO::FETCH_* outside a recognised method call or 'fetch' array key is not transformed.
$mode = \PDO::FETCH_ASSOC;
$ternary = $useAssoc ? \PDO::FETCH_ASSOC : \PDO::FETCH_OBJ;

function example($mode = \PDO::FETCH_ASSOC) {
    return $mode;
}

?>
