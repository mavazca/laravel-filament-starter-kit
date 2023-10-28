<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = '15s';

    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        return [
            Stat::make(__('Total users'), User::count())
                ->description(__('Total users in app'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),
            Stat::make(__('Total roles'), Role::count())
                ->description(__('Total roles in app'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),
            Stat::make(__('Total permissions'), Permission::count())
                ->description(__('Total permissions in app'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, 3, 5, 8]),
        ];
    }
}
