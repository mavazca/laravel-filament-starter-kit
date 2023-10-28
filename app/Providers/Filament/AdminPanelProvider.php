<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Backups;
use App\Http\Middleware\RedirectNotActiveUser;
use BezhanSalleh\FilamentLanguageSwitch\FilamentLanguageSwitchPlugin;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\Column;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->colors([
//                'primary' => Color::hex('#014bde'),
                'primary' => Color::Emerald,
            ])
            ->font('Nunito')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->favicon(asset('images/favicon.svg'))
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
//                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                RedirectNotActiveUser::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentSpatieLaravelBackupPlugin::make()
                    ->usingPolingInterval('10s') // default value is 4s
                    ->usingPage(Backups::class),
                FilamentLanguageSwitchPlugin::make(),
                BreezyCore::make()
                    ->myProfile(
                        hasAvatars: true,
                        slug: 'profile',
                    )
                    ->avatarUploadComponent(fn ($fileUpload) => $fileUpload->disableLabel())
                    ->enableTwoFactorAuthentication()
                    ->enableSanctumTokens(),
            ])
            ->navigationGroups([
                'Administrative',
                'Settings',
            ]);
    }

    public function boot(): void
    {
        Column::configureUsing(function (Column $column): void {
            $column
                ->toggleable()
                ->searchable();
        });

        Filament::registerNavigationGroups([
            __('Administrative'),
            __('Settings'),
        ]);

        Filament::serving(function () {
            $navigationItem = [];

            if (Gate::allows('manage debug')) {
                $navigationItem[] = NavigationItem::make()
                    ->label(__('Debug'))
                    ->url(route('telescope'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-presentation-chart-line')
                    ->activeIcon('heroicon-s-presentation-chart-line')
                    ->group(__('Settings'));
            }

            if (Gate::allows('manage queue')) {
                $navigationItem[] = NavigationItem::make()
                    ->label(__('Queue'))
                    ->url(route('horizon.index'), shouldOpenInNewTab: true)
                    ->icon('heroicon-o-queue-list')
                    ->activeIcon('heroicon-s-queue-list')
                    ->group(__('Settings'));
            }

            Filament::registerNavigationItems($navigationItem);
        });
    }
}
