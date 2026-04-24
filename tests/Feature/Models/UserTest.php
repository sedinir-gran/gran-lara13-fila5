<?php

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('has correct fillable attributes', function () {
    $user = new User;

    $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'is_admin',
    ];

    $actual = $user->getFillable();

    expect($actual)->toEqualCanonicalizing($fillable);
});

it('has correct hidden attributes', function () {
    $user = new User;

    $hidden = [
        'password',
        'remember_token',
    ];
    $actual = $user->getHidden();

    expect($actual)->toEqualCanonicalizing($hidden);
});

it('casts attributes correctly', function () {
    // $now = now();
    $user = User::factory()->make([
        'is_admin' => true,
        'password' => bcrypt('secret'),
        // 'email_verified_at' => $now,
    ]);

    expect($user->is_admin)->toBeBool()->toBeTrue();
    expect(Hash::check('secret', $user->password))->toBeTrue();
    // expect($user->email_verified_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

it('creates an valid user using factory', function () {
    $user = User::factory()->create();

    expect($user)
        ->and($user->name)->not()->toBeNull()
        ->and($user->email)->not()->toBeNull()
        ->and($user->password)->not()->toBeNull()
        ->and($user->avatar_url)->not()->toBeNull()
        ->and($user->is_admin)->not()->toBeNull();
});

it('returns the correct title', function () {
    $user = User::factory()->make([
        'name' => 'John Doe',
    ]);

    expect($user->title())->toBe('John Doe');
});
