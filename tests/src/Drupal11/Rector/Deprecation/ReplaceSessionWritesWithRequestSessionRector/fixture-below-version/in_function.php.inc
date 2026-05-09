<?php

function my_module_store_data(string $key, mixed $value): void {
    $_SESSION[$key] = $value;
    $_SESSION['prefix_' . $key] = $value;
}

?>
-----
<?php

function my_module_store_data(string $key, mixed $value): void {
    $_SESSION[$key] = $value;
    $_SESSION['prefix_' . $key] = $value;
}

?>
