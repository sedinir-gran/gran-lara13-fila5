<?php

namespace App\Traits;

use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Provides Filament panel integration for Eloquent models.
 *
 * Implements the {@see FilamentUser} and
 * {@see HasAvatar} contracts by supplying:
 *   - Panel-level access control via {@see canAccessPanel()}
 *   - Avatar URL resolution from filesystem storage via {@see getFilamentAvatarUrl()}
 *   - Automatic avatar file cleanup on model update/delete
 *
 * **Requirements**
 *   - The consuming class must extend {@see Model}.
 *   - The `avatar_url` column must exist on the model's database table.
 *   - The `is_admin` column (boolean) must exist for admin-panel access control.
 *   - Run `php artisan storage:link` to expose the storage disk publicly.
 *   - Set `FILESYSTEM_DISK=public` (or configure `filesystems.default`) in `.env`.
 *
 * @mixin Model
 *
 * @see FilamentUser
 * @see HasAvatar
 * @see FilamentTraitTest
 *
 * @property bool $is_admin Whether the model owner has administrator privileges.
 * @property string|null $avatar_url Relative storage path to the user's avatar image.
 * @property string $name Display name shown in the Filament top bar.
 */
trait FilamentTrait
{
    /**
     * Determine whether the authenticated user may access the given Filament panel.
     *
     * The `admin` panel is restricted to models where `is_admin` is truthy.
     * All other panels are publicly accessible by any authenticated user.
     *
     * @tested-by \Tests\Feature\FilamentTraitTest::it_controls_access_to_panels
     *
     * @see FilamentUser::canAccessPanel()
     *
     * @param  Panel  $panel  The Filament panel being requested.
     * @return bool `true` if access should be granted, `false` otherwise.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->is_admin,
            default => true,
        };
    }

    /**
     * Resolve the public URL for the user's Filament avatar image.
     *
     * Delegates to {@see Storage::url()} using the
     * path stored in `avatar_url`. Returns `null` when no avatar has been set,
     * allowing Filament to fall back to its default placeholder.
     *
     * @tested-by \Tests\Feature\FilamentTraitTest::it_gets_avatar_url_or_null
     *
     * @see HasAvatar::getFilamentAvatarUrl()
     *
     * @return string|null The fully-qualified public URL to the avatar, or `null` if the user has no avatar configured.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        if (empty($this->avatar_url)) {
            return null;
        }

        /** @var string $avatar_url */
        $avatar_url = $this->avatar_url;

        return Storage::url($avatar_url);
    }

    /**
     * Delete the model record and its associated avatar file from storage.
     *
     * Overrides the parent `delete()` to ensure the avatar file is removed from
     * the filesystem before the database row is deleted, preventing orphaned files.
     * Delegates to the parent implementation when one exists.
     *
     * @tested-by \Tests\Feature\FilamentTraitTest::it_deletes_avatar_file_and_user_from_database
     *
     * @see deleteAvatarFile()
     *
     * @return bool|null `true` on success, `false` on failure, or `null` when the parent implementation returns `null` (e.g. soft-delete models).
     */
    public function delete(): ?bool
    {
        $this->deleteAvatarFile();
        if (($parent = get_parent_class($this))
            && method_exists($parent, 'delete')) {
            return parent::delete();
        }

        return true;
    }

    /**
     * Update the user's attributes and handle avatar file changes.
     * If the avatar URL is changed, the old avatar file will be deleted.
     *
     * @tested-by \Tests\Feature\FilamentTraitTest::it_deletes_avatar_file_if_exists_on_update
     * @tested-by \Tests\Feature\FilamentTraitTest::it_does_not_delete_avatar_if_unchanged_on_update
     *
     * @see deleteAvatarFile()
     *
     * @param  array<string, mixed>  $attributes  The attributes to update.
     * @param  array<mixed>  $options  The options for the update operation.
     * @return bool Whether the update was successful.
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        $theresAvatar = \array_key_exists('avatar_url', $attributes);

        $oldAvatar = $this->avatar_url;

        $newAvatar = $attributes['avatar_url'] ?? null;

        if ($theresAvatar && $oldAvatar !== $newAvatar) {
            $this->deleteAvatarFile();
        }

        if (($parent = get_parent_class($this))
            && method_exists($parent, 'update')) {
            return parent::update($attributes, $options);
        }

        return true;
    }

    /**
     * Remove the avatar file from storage and clear the `avatar_url` attribute.
     *
     * Resolves the active filesystem disk from `filesystems.default`, falling back
     * to `'public'` if the configuration value is not a string. After deleting the
     * file, the model's `avatar_url` column is set to `null` and persisted via
     * {@see Model::forceFill()} to bypass any
     * mass-assignment restrictions.
     *
     * A no-op when `avatar_url` is empty or `null`.
     *
     * @see Storage::disk()
     *
     * @tested-by \Tests\Feature\FilamentTraitTest::it_deletes_avatar_file_and_user_from_database
     * @tested-by \Tests\Feature\FilamentTraitTest::it_deletes_avatar_file_if_exists_on_update
     */
    private function deleteAvatarFile(): void
    {
        if (empty($this->avatar_url)) {
            return;
        }

        $disk = is_string(config('filesystems.default'))
            ? config('filesystems.default')
            : 'public';

        /** @var string $path */
        $path = $this->avatar_url;
        Storage::disk($disk)->delete($path);

        $this->forceFill(['avatar_url' => null])->save();
    }
}
