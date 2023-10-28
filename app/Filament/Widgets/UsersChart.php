<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;

class UsersChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string | Htmlable | null
    {
        return __('Users created per month');
    }

    protected function getData(): array
    {
        $data = $this->getUsersPerMonth();

        return [
            'datasets' => [
                [
                    'label' => __('Users created'),
                    'data' => $data['usersPerMonth'],
                ],
            ],
            'labels' => $data['months'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getUsersPerMonth(): array
    {
        $now = Carbon::now();

        $usersPerMonth = [];
        $months = [];

        for ($month = 1; $month <= 12; $month++) {
            $usersPerMonth[] = User::whereMonth('created_at', $now->month($month))->count();
            $months[] = $now->month($month)->format('M');
        }

        return [
            'usersPerMonth' => $usersPerMonth,
            'months' => $months,
        ];
    }
}
