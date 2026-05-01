<?php

// When '#type' is assigned via a variable rather than a string literal the
// rector must not touch it — static analysis cannot guarantee the value.
$type = 'fieldgroup';
$form['account']['#type'] = $type;
