<?php

function file_functions() {
    file_copy();
    file_move();
    file_save_data();
    $uri1 = file_build_uri('path/to/file.txt');
    $path = 'path/to/other/file.png';
    $uri2 = file_build_uri($path);
}
