<?php

function file_functions() {
    \Drupal::service('file.repository')->copy();
    \Drupal::service('file.repository')->move();
    \Drupal::service('file.repository')->writeData();
}
