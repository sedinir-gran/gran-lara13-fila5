<?php

use App\Models\User;

test('the home page returns a successful response', function () {
    $url = config('app.url');
    expect($url)->toBeString();
    assert(is_string($url));
    $this->actingAs(User::factory()->create())->get("{$url}/".strtolower(__('Home')))->assertOk();
});

test('the login page is working for regular user', function () {

    $user = User::factory()->create(['is_admin' => false]);

    $url = config('app.url');
    expect($url)->toBeString();
    assert(is_string($url));

    $home = strtolower(__('Home'));

    visit("{$url}/{$home}/login")
        ->type('#form\.email', $user->email)
        ->type('#form\.password', 'password')
        ->press('Login')
        ->assertPathIs('/'.rawurlencode($home))
        ->assertNoSmoke();
});
