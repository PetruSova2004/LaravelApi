<?php
/**
 * Created by PhpStorm.
 * User: note
 * Date: 28.01.2021
 * Time: 23:08
 */

namespace App\Modules\Admin\Analitics\Services;

use DateService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnaliticDataService
{

    public function getAnalitic($request) // нам нужно вызвать процедуру из БД
    {
        $dateStart = Carbon::now();
        if($request->dateStart && DateService::isValid($dateStart, "d.m.Y")) {
            $dateStart = Carbon::parse($request->dateStart); // Парсим дату которая к нам приходит
        }

        $dateEnd = Carbon::now();
        if($request->dateEnd && DateService::isValid(dateEnd, "d.m.Y")) { // Проверяем корректность передаваемого времени
            $dateEnd = Carbon::parse($request->dateEnd);
        }

        $leadsData = DB::select(
            'CALL countLeads("'.$dateStart->format('Y-m-d') . '","'.$dateEnd->format('Y-m-d') . '")' // Вызываем метод countLeads из миграции и передаем в качестве параметров $dateStart и $dateEnd
        );

        return $leadsData; // Вернём подсчёт лидов по определённым параметрам для каждого пользователя за определённый промежуток времени
    }
}
