<?php

use Illuminate\Foundation\Application;

$app = new Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Cache\Factory::class,
    Illuminate\Cache\CacheManager::class
);

$app->singleton(
    Illuminate\Contracts\Queue\Factory::class,
    Illuminate\Queue\QueueManager::class
);

// Bootstrap the application
$app->bootstrapWith([
    'Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables',
    'Illuminate\Foundation\Bootstrap\LoadConfiguration',
    'Illuminate\Foundation\Bootstrap\HandleExceptions',
    'Illuminate\Foundation\Bootstrap\RegisterFacades',
    'Illuminate\Foundation\Bootstrap\RegisterProviders',
    'Illuminate\Foundation\Bootstrap\BootProviders',
]);

return $app;
