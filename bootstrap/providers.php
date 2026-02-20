<?php

return array_filter([
    App\Providers\AppServiceProvider::class,
    App\Providers\MoonShineServiceProvider::class,
    class_exists(\Laravel\Telescope\TelescopeApplicationServiceProvider::class)
        ? App\Providers\TelescopeServiceProvider::class
        : null,
]);
