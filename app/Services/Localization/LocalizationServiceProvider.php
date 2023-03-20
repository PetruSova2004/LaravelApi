<?php

namespace App\Services\Localization;

use Illuminate\Support\ServiceProvider;

class LocalizationServiceProvider extends ServiceProvider // данный класс является поставщиком услуг
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('Localization', function($app) { //  с помощью singleton() - мы говорим фреймворку что необходимо создать единственный экземпляр определенного класса и его нужно положить в определенную ячейку под определенном именем; 1 - Имя ячейки, 2 - функцию, которая создаст объект необходимого класса
            return new Localization();
        });

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
