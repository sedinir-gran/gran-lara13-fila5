<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\HomePanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    HomePanelProvider::class,
];
