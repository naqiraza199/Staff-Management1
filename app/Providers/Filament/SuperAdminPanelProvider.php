<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\SuperAdminOnly;
use App\Filament\SuperAdmin\Widgets\SuperAdminStats;

class SuperAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('superAdmin')
            ->path('superAdmin')
            ->login()
            ->favicon(asset('fav.png'))
            ->brandLogo(fn () => view('filament.logo'))
            ->navigation(true)
             ->colors([
                'primary' => Color::hex('#6860FF'),
                'brown' => Color::hex('#8f6232'),
                'lightgreen' => Color::hex('#86de28'),
                'yee' => Color::hex('#f5dd02'),
                'white' => Color::hex('#FFFFFF'),
                'rado' => Color::hex('#008000'),
                'muted' => Color::hex('#C5C5C5'),
                'darkk' => Color::hex('#BE3144'),
                'ngree' => Color::hex('#643843'),
                'stripe' => Color::hex('#6860FF'),
                'ligi' => Color::hex('#13c4ffff'),
                'blackk' => Color::hex('#101016ff'),
            ])
            ->discoverResources(in: app_path('Filament/SuperAdmin/Resources'), for: 'App\\Filament\\SuperAdmin\\Resources')
            ->discoverPages(in: app_path('Filament/SuperAdmin/Pages'), for: 'App\\Filament\\SuperAdmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/SuperAdmin/Widgets'), for: 'App\\Filament\\SuperAdmin\\Widgets')
            ->widgets([
                SuperAdminStats::class,
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
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
                    SuperAdminOnly::class,
            ]);
    }
}
