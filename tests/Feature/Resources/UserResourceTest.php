<?php

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('can list users', function () {
    $users = User::factory()->count(3)->create();

    Livewire::test(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->assertStatus(200);
});

it('can render create page', function () {
    Livewire::test(CreateUser::class)
        ->assertSchemaExists('form')
        ->assertStatus(200);
});

it('can create a user', function () {
    $newData = User::factory()->make();
    $password = 'password';

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'TestUser',
            'email' => $newData->email,
            'password' => $password,
            // 'is_admin' is boolean, radio uses int 0/1 keys implicitly
            'is_admin' => (int) $newData->is_admin,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(User::class, [
        'email' => $newData->email,
    ]);
});

it('can render edit page', function () {
    $user = User::factory()->create();

    Livewire::test(EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertSchemaExists('form')
        ->assertSchemaStateSet([
            'name' => $user->name,
            'email' => $user->email,
        ])
        ->assertStatus(200);
});

it('can edit a user', function () {
    $user = User::factory()->create();
    $newName = 'Updated Name'; // Short name to avoid maxLength(25) validation

    Livewire::test(EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->fillForm([
            'name' => $newName,
            'is_admin' => (int) $user->is_admin,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->refresh()->name)->toBe($newName);
});

it('can delete a user', function () {
    // We need to create another user to delete, preserving the acting user
    $userToDelete = User::factory()->create();

    Livewire::test(EditUser::class, [
        'record' => $userToDelete->getRouteKey(),
    ])
        ->callAction('delete')
        ->assertHasNoFormErrors();

    $this->assertModelMissing($userToDelete);
});
