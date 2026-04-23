<?php

namespace Tests\Feature\Database;

use Illuminate\Support\Facades\Schema;

it('has users table')
    ->expect(fn () => Schema::hasTable('users'))
    ->toBeTrue();

it('has the expected columns', function () {
    $expected = [
        'id',
        'name',
        'email',
        'email_verified_at',
        'password',
        'is_admin',
        'avatar_url',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    $actual = Schema::getColumnListing('users');

    expect($actual)->toEqualCanonicalizing($expected);
});
