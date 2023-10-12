<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups as BaseBackups;

class Backups extends BaseBackups
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('manage backups');
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->can('manage backups'), 403);
    }
}
