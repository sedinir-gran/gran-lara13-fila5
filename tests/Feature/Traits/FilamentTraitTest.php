<?php

use App\Models\User;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

it('controls access to panels', function () {
    $admin = User::factory(
        ['is_admin' => true]
    )->create();

    $user = User::factory(
        ['is_admin' => false]
    )->create();

    /** @var Panel&MockInterface $adminPanel */
    $adminPanel = Mockery::mock(Panel::class);
    $adminPanel->shouldReceive('getId')->andReturn('admin');

    expect($admin->canAccessPanel($adminPanel))->toBeTrue();
    expect($user->canAccessPanel($adminPanel))->toBeFalse();

    /** @var Panel&MockInterface $otherPanel */
    $otherPanel = Mockery::mock(Panel::class);
    $otherPanel->shouldReceive('getId')->andReturn('other');

    expect($admin->canAccessPanel($otherPanel))->toBeTrue();
    expect($user->canAccessPanel($otherPanel))->toBeTrue();

});

it('gets avatar url or null', function () {
    $avatar = User::factory(
        ['avatar_url' => 'avatars/test.png']
    )->create();

    $null = User::factory(
        ['avatar_url' => null]
    )->create();

    expect($avatar->getFilamentAvatarUrl())->toBe(Storage::url('avatars/test.png'));
    expect($null->getFilamentAvatarUrl())->toBeNull();
});

it('deletes avatar file if exists on update', function () {
    Storage::fake('public');
    config()->set('filesystems.default', 'public');

    $user = User::factory()->create([
        'avatar_url' => 'avatars/test.png',
    ]);

    Storage::disk('public')->put('avatars/test.png', 'fake');

    expect(Storage::disk('public')->exists('avatars/test.png'))->toBeTrue();

    $user->update(['avatar_url' => 'avatars/novo.png']);

    expect(Storage::disk('public')->exists('avatars/test.png'))->toBeFalse();
});

it('does not delete avatar if unchanged on update', function () {
    $user = User::factory()->create([
        'avatar_url' => 'avatars/test.png',
    ]);

    Storage::disk('public')->put('avatars/test.png', 'fake');
    expect(Storage::disk('public')->exists('avatars/test.png'))->toBeTrue();

    $user->update(['name' => 'New Name']);

    expect(Storage::disk('public')->exists('avatars/test.png'))->toBeTrue();
});

it('deletes avatar file and user from database', function () {
    Storage::fake('public');
    config()->set('filesystems.default', 'public');

    $avatarPath = 'avatars/to-delete.png';

    $user = User::factory()->create([
        'avatar_url' => $avatarPath,
    ]);

    Storage::disk('public')->put($avatarPath, 'fake');
    expect(Storage::disk('public')->exists($avatarPath))->toBeTrue();

    $user->delete();

    expect(Storage::disk('public')->exists($avatarPath))->toBeFalse();
    expect(User::find($user->id))->toBeNull();
});
