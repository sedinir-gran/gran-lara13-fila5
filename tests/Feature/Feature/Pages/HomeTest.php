<?php

test('the home page returns a successful response', function () {
    $url = config('app.url');
    assert(is_string($url));
    visit("{$url}/")->assertNoSmoke();
});
