<?php
/**
 * Created by PhpStorm.
 * User: note
 * Date: 30.01.2021
 * Time: 9:06
 */

namespace App\Services\Date\Facade;


use Illuminate\Support\Facades\Facade;

class DateService extends Facade // Фасад - Предоставляет нам упрощенный доступ к объектам классов из ServiceContainer
{

    protected static function getFacadeAccessor()
    {
        return 'dateCheck'; // Указываем имя ячейки из ServiceContainer которую нам нужно вернуть
    }

}
