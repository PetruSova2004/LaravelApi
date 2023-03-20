<?php
/**
 * Created by PhpStorm.
 * User: note
 * Date: 30.01.2021
 * Time: 9:04
 */

namespace App\Services\Date;


class DateCheck
{

    public static function isValid($str_dt, $str_dateformat = "Y-m-d"): bool
    {
        $date = \DateTime::createFromFormat($str_dateformat, $str_dt); // Пытаемся создать объект даты используя формат $str_dateformat из строки которая приходит в $str_dt

        if ($date && (int)$date->format("Y") < 1900) { // Приводим Год к int и проверяем чтобы если он меньше 1900
            return false;
        }

        return $date && \DateTime::getLastErrors()["warning_count"] == 0 && \DateTime::getLastErrors()["error_count"] == 0; // Проверяем были ли ошибки при создании даты

    }
}
