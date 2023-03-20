<?php
/**
 * Created by PhpStorm.
 * User: note
 * Date: 01.02.2021
 * Time: 22:44
 */

namespace App\Services\Filters;


use Illuminate\Database\Eloquent\Builder;

interface Filterable // Этот интерфейс будет имплементировать все фильтры которые мы создадим
{
    public static function apply(Builder $builder, $value);
}
