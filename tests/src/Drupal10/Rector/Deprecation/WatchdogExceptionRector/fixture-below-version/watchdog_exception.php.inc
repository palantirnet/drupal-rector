<?php

function simple_example() {
    watchdog_exception('update', $exception);
}

/**
 * A simple example.
 */
function advanced() {
    watchdog_exception('update', $exception, 'My custom message @foo', ['@foo' => 'bar'], RfcLogLevel::CRITICAL, 'http://example.com');

    watchdog_exception('update', $exception, 'My custom message @foo', ['@foo' => 'bar']);

    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => \Drupal\Core\Utility\Error::logException(\Drupal::logger('update'), $exception), fn() => watchdog_exception('update', $exception));
}
?>
-----
<?php

function simple_example() {
    watchdog_exception('update', $exception);
}

/**
 * A simple example.
 */
function advanced() {
    watchdog_exception('update', $exception, 'My custom message @foo', ['@foo' => 'bar'], RfcLogLevel::CRITICAL, 'http://example.com');

    watchdog_exception('update', $exception, 'My custom message @foo', ['@foo' => 'bar']);

    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => \Drupal\Core\Utility\Error::logException(\Drupal::logger('update'), $exception), fn() => watchdog_exception('update', $exception));
}
?>
