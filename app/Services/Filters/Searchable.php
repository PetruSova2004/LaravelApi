<?php
/**
 * Created by PhpStorm.
 * User: note
 * Date: 01.02.2021
 * Time: 22:41
 */

namespace App\Services\Filters;


use Illuminate\Http\Request;

interface Searchable // Данный интерфейс нужен для создания специальных классов для фильтраций по конкретным моделям
{
    public function apply(Request $filters);
}
