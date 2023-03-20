<?php
/**
 * Created by PhpStorm.
 * User: note
 * Date: 28.11.2020
 * Time: 8:19
 */

namespace App\Services\Localization;


use Illuminate\Support\Facades\App;

class Localization
{
    public function locale() { // формируем локализацию
        $locale = request()->segment(1); // сюда будет попадать код языка('en', 'ru') из URL запроса
        if($locale && in_array($locale, config('app.locales'))) {
            App::setLocale($locale); // устанавливаем локализацию
            return $locale;// получаем то что установленно('en','ru')
        }

//        return '';
    }
}
