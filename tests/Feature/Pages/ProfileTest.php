<?php

use App\Models\User;

test('the profile page returns a successful response', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]))->get('/admin/minha-conta')->assertOk();
});

test('the profile page has no smoke', function () {
    $url = config('app.url');
    assert(is_string($url));
    $this->actingAs(User::factory()->create(['is_admin' => true]));
    visit("{$url}/admin/minha-conta")->assertNoSmoke();
});
