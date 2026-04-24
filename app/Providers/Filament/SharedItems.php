<?php

namespace App\Providers\Filament;

use Filament\Actions\Action;
use Filament\Contracts\Plugin;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;

class SharedItems
{
    /**
     * @return array<Plugin>
     */
    public static function plugins(): array
    {
        return [
            FilamentEditProfilePlugin::make()
                ->setIcon('heroicon-o-user-circle')
                ->setSort(12)
                ->shouldShowEmailForm(false)
                ->shouldShowAvatarForm(
                    value: true,
                    directory: 'avatars',
                    rules: 'mimes:jpeg,png|max:1024'
                )
                ->slug(__('my-account'))
                ->setTitle(' ')
                ->setNavigationLabel(__('My Account')),
        ];
    }

    /**
     * @return array<Action>
     */
    public static function menu(string $panel_id = ''): array
    {
        return [
            Action::make('account')
                ->label('Minha Conta')
                ->icon('heroicon-o-user-circle')
                ->url(fn (): string => EditProfilePage::getUrl()),
        ];
    }
}
