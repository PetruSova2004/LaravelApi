<?php

namespace App\Providers;

use App\Services\Localization\LocalizationService;
use Illuminate\Support\ServiceProvider;
use Route;

class ModularProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //

        $modules = config('modular.modules');
        $path = config('modular.path'); // Путь к app/Modules

//        dd($modules);
//        dd($path);

        if($modules) {
            Route::group([
//                'prefix'=>LocalizationService::locale()
            ], function() use($modules, $path) {

                foreach ($modules as $mod => $submodules) {
                    foreach ($submodules as $key => $sub) {

                        $relativePath = "/$mod/$sub"; // путь к дочернему модулю(Admin/User)


                        Route::middleware('web')
                            ->group(function() use($mod, $sub, $relativePath, $path) {
                                $this->getWebRoutes($mod, $sub, $relativePath, $path);
                            });

                        Route::prefix('api')
                            ->middleware('api')
                            ->group(function() use($mod, $sub, $relativePath, $path) {
                                $this->getApiRoutes($mod, $sub, $relativePath, $path);
                            });
                    }
                }

            });
        }

        $this->app['view']->addNamespace('Pub', base_path() . '/resources/views/Pub'); // Pub:: - будет ссылаться на папку '/resources/views/Pub'
        $this->app['view']->addNamespace('Admin', base_path() . '/resources/views/Admin');
    }

    private function getWebRoutes($mod, $sub, $relativePath, $path) // метод для формирования веб маршрутов
    {

        $routesPath = $path.$relativePath.'/Routes/web.php'; // Путь к файлу с маршрутами из модулей
        if(file_exists($routesPath)) {

            if($mod != config('modular.groupWithoutPrefix')) { // если имя модуля не совпадает со строкой которая находится по пути('Pub')
                Route::group(
                    [
                        'prefix' => strtolower($mod),
                        'middleware' => $this->getMiddleware($mod)
                    ],
                    function() use ($mod, $sub, $routesPath) {
                        Route::namespace("App\Modules\\$mod\\$sub\Controllers")->
                        group($routesPath);
                    }
                );
            }
            else {
                Route::namespace("App\Modules\\$mod\\$sub\Controllers")->
                middleware($this->getMiddleware($mod))->
                group($routesPath);
            }

        }

    }

    private function getApiRoutes($mod, $sub, $relativePath, $path) // метод для формирования api маршрутов
    {
        $routesPath = $path.$relativePath.'/Routes/api.php';
        if(file_exists($routesPath)) {
            Route::group(
                [
                    'prefix' => strtolower($mod),
                    'middleware' => $this->getMiddleware($mod, 'api')
                ],
                function() use ($mod, $sub, $routesPath) {
                    Route::namespace("App\Modules\\$mod\\$sub\Controllers")->
                    group($routesPath);
                }
            );
        }
    }

    private function getMiddleware($mod, $key = 'web')
    {
        $middleware = [];

        $config = config('modular.groupMidleware');

        if(isset($config[$mod])) { // существует ли в $config имя родительского модуля
            if(array_key_exists($key, $config[$mod])) {
                // array_merge — Сливает один или большее количество массивов
                $middleware = array_merge($middleware, $config[$mod][$key]);
            }
        }

        return $middleware;
    }
}
