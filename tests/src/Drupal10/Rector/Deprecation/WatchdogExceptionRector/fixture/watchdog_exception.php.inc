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
}
?>
-----
<?php

function simple_example() {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => watchdog_exception('update', $exception), fn() => \Drupal\Core\Utility\Error::logException(\Drupal::logger('update'), $exception));
}

/**
 * A simple example.
 */
function advanced() {
    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => watchdog_exception('update', $exception, 'My custom message @foo', ['@foo' => 'bar', 'link' => 'http://example.com'], RfcLogLevel::CRITICAL, 'http://example.com'), fn() => \Drupal\Core\Utility\Error::logException(\Drupal::logger('update'), $exception, 'My custom message @foo', ['@foo' => 'bar', 'link' => 'http://example.com'], RfcLogLevel::CRITICAL));

    \Drupal\Component\Utility\DeprecationHelper::backwardsCompatibleCall(\Drupal::VERSION, '10.1.0', fn() => watchdog_exception('update', $exception, 'My custom message @foo', ['@foo' => 'bar']), fn() => \Drupal\Core\Utility\Error::logException(\Drupal::logger('update'), $exception, 'My custom message @foo', ['@foo' => 'bar']));
}
?>
