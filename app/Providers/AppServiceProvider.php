<?php

namespace App\Providers;

use App\Models\HomepageContent;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $whatsappPhone = SiteSetting::value('whatsapp_phone');

        if ($whatsappPhone) {
            Config::set('seo.whatsapp_phone', $whatsappPhone);
        }

        View::composer('*', function ($view): void {
            $view->with(
                'siteNavigationMenu',
                Schema::hasTable('homepage_contents')
                    ? HomepageContent::currentNavigationMenu()
                    : HomepageContent::defaultNavigationMenu()
            );
        });
    }
}
