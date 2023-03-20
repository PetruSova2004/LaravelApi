<?php

namespace App\Services\Date\Provider;

use App\Services\Date\DateCheck;
use Illuminate\Support\ServiceProvider;

class DateCheckServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    // ServiceContainer - Это хранилище функционала фреймворка;
    // Providers - Нужны для того, чтобы загрузить в ServiceContainer нужную нам функциональность(классы);
    // Facade - Предоставляет нам упрощенный доступ к объектам классов из ServiceContainer
    public function register() // Данный метод зарегистрирует нужный нам класс в ServiceContainer
    {
        $this->app->bind('dateCheck', DateCheck::class); // $app - Объект ServiceContainer

        // bind() - Связывает объект определённого класса с именуемой ячейкой в ServiceContainer; dateCheck - имя ячейки где будет храниться объект нужного класса в ServiceContainer, 2 аргумент это объект класса который будет положен в данную ячейку
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
