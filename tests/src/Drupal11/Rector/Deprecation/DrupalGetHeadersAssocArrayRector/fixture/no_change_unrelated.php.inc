<?php

// Unrelated class with an identically-named drupalGet() method must not be transformed.
class UnrelatedHttpClient {
    public function drupalGet($path, array $options = [], array $headers = []) {
        return null;
    }
}

function unrelated_caller() {
    $client = new UnrelatedHttpClient();
    $client->drupalGet('/path', [], ['X-Requested-With: XMLHttpRequest']);
    $client->drupalGet('', [], ['Accept-Language' => NULL]);
}

// Untyped variable: rector cannot prove it is a BrowserTestBase instance.
function untyped_caller($thing) {
    $thing->drupalGet('/path', [], ['X-Requested-With: XMLHttpRequest']);
}
?>
-----
<?php

// Unrelated class with an identically-named drupalGet() method must not be transformed.
class UnrelatedHttpClient {
    public function drupalGet($path, array $options = [], array $headers = []) {
        return null;
    }
}

function unrelated_caller() {
    $client = new UnrelatedHttpClient();
    $client->drupalGet('/path', [], ['X-Requested-With: XMLHttpRequest']);
    $client->drupalGet('', [], ['Accept-Language' => NULL]);
}

// Untyped variable: rector cannot prove it is a BrowserTestBase instance.
function untyped_caller($thing) {
    $thing->drupalGet('/path', [], ['X-Requested-With: XMLHttpRequest']);
}
?>
