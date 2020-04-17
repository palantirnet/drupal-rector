<?php

/**
 * A simple example using the minimum required (all) number of arguments.
 */
function simple_example() {
  $source = '/test/directory';
  $destination = '/test/directory_2';
  file_unmanaged_copy($source, $destination,FILE_CREATE_DIRECTORY);
}