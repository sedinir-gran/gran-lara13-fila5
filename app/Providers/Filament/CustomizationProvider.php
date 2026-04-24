<?php

namespace App\Providers\Filament;

use App\Models\User;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class CustomizationProvider extends ServiceProvider
{
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::GLOBAL_SEARCH_AFTER,
            function (): string {
                if (! Auth::check()) {
                    return '';
                }

                $user = Auth::user();
                assert($user instanceof User);

                return '<span class="ms-2 text-sm font-medium text-gray-700 dark:text-white">'
                  .$user->title().'</span>';
            });
    }
}
