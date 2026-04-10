<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class FilamentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('filament')
            ->path('admin')
            ->login(Login::class)
            ->brandName('Elite Locadora')
            ->locale('pt_BR')
            ->font('Inter')
            ->darkMode(true, true)
            ->maxContentWidth(Width::Full)
            ->colors([
                'primary' => [
                    50  => '#eff6ff',
                    100 => '#dbeafe',
                    200 => '#bfdbfe',
                    300 => '#93c5fd',
                    400 => '#60a5fa',
                    500 => '#3b82f6',
                    600 => '#2563eb',
                    700 => '#1d4ed8',
                    800 => '#1e40af',
                    900 => '#1e3a8a',
                    950 => '#172554',
                ],
                'danger' => Color::Rose,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => [
                    50  => '#fff7ed',
                    100 => '#ffedd5',
                    200 => '#fed7aa',
                    300 => '#fdba74',
                    400 => '#fb923c',
                    500 => '#f97316',
                    600 => '#ea580c',
                    700 => '#c2410c',
                    800 => '#9a3412',
                    900 => '#7c2d12',
                    950 => '#431407',
                ],
            ])
            ->navigationGroups([
                NavigationGroup::make('Gestão de Frota')
                    ->icon('heroicon-o-truck'),
                NavigationGroup::make('Cadastros')
                    ->icon('heroicon-o-user-group'),
                NavigationGroup::make('Operacional')
                    ->icon('heroicon-o-clipboard-document-list'),
                NavigationGroup::make('Financeiro')
                    ->icon('heroicon-o-banknotes'),
                NavigationGroup::make('Relatórios')
                    ->icon('heroicon-o-chart-bar-square'),
                NavigationGroup::make('Serviços')
                    ->icon('heroicon-o-wrench-screwdriver'),
                NavigationGroup::make('CMS / Site')
                    ->icon('heroicon-o-globe-americas')
                    ->collapsed(),
                NavigationGroup::make('Configuração')
                    ->icon('heroicon-o-cog-8-tooth')
                    ->collapsed(),
                NavigationGroup::make('Sistema')
                    ->icon('heroicon-o-server-stack')
                    ->collapsed(),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17rem')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->renderHook('panels::head.end', fn () => view('filament.partials.custom-theme'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
