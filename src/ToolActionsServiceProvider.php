<?php

namespace AwesomeNova;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

final class ToolActionsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(ServingNova::class, function () {
            Nova::script('awesome-nova-tool-actions', dirname(__DIR__) . '/dist/js/actions.js');
        });
    }
}
