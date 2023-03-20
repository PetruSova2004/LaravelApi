<?php

namespace App\Modules\Admin\Menu\Models;

use App\Modules\Admin\Role\Models\Permission;
use App\Modules\Admin\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    const MENU_TYPE_FRONT = 'front';
    const MENU_TYPE_ADMIN = 'admin';

    ///perms
    public function perms()
    {
        return $this->belongsToMany(Permission::class, 'permission_menu');
    }


    public function scopeFrontMenu($query, User $user)
    { // этот метод вытаскивает только пункты меню фронт приложения которые также присутствуют в таблице permission_menu

        return $query->
        where('type', self::MENU_TYPE_FRONT)->
        whereHas('perms', function ($q) use ($user) { // Выбираем те меню у которых есть связь 'perms'

            $arr = collect($user->getMergedPermissions())->map(function ($item) { // getMergedPermissions() - вернет нам коллекцию привилегии пользователя;
                return $item['id']; // получим ид привилегий
            });

            $q->whereIn('id', $arr->toArray()); // Где ид привилегии соответствует коллекций из всех привилегии пользователя
        });
    }


//    public function scopeFrontMenu($query, User $user)
//    { // этот метод вытаскивает только пункты меню фронт приложения
//
//        return $query->
//        where('type', self::MENU_TYPE_FRONT);
//    }

    public function scopeMenuByType($query, $type)
    { // вытаскиваем меню по типу
        return $query->where('type', $type)->orderBy('parent')->orderBy('sort_order');
    }
}
