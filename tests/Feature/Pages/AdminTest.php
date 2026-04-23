<?php

use App\Models\User;
use Filament\Auth\Pages\Login;
use Livewire\Livewire;

test('the admin page returns a successful response', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]))->get('/admin')->assertOk();
});

test('the login page is working for admin user', function () {
    $user = User::factory()->create(['is_admin' => true]);

    Livewire::test(Login::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertRedirect('/admin');
});
