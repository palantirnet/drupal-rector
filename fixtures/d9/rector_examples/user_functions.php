<?php

function user_functions() {
    // user_password().
    $password = user_password();
    $other_password = user_password(8);
    $password_length = 12;
    $last_password = user_password($password_length);
}
