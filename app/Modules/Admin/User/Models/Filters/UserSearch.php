<?php
/**
 * Created by PhpStorm.
 * User: note
 * Date: 01.02.2021
 * Time: 22:47
 */

namespace App\Modules\Admin\User\Models\Filters;


use App\Modules\Admin\User\Models\User;
use App\Services\Filters\BaseSearch;
use App\Services\Filters\Searchable;

class UserSearch implements Searchable // // Этот класс будет реализовывать фильтрацию по Пользователям
{
    const MODEL = User::class; // В этой константе сохраняем имя класса той сущности(модели) с которой будет работать последующие создаваемые фильтры
    use BaseSearch;
}
