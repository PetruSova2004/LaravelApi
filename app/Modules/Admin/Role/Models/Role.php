<?php

namespace App\Modules\Admin\Role\Models;

use App\Modules\Admin\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'alias',
        'title',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() { // У Роли может быть определённый набор пользователей
        return $this->belongsToMany(User::class); // belongsToMany() - реализует связь многим ко многим
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function perms() { // У Роли может быть определённый набор привилегий
        return $this->belongsToMany(Permission::class);
    }


    public function savePermissions($perms) { // данный метод будет сохранять определённый набор привилегии
//        dd($perms);
        if(!empty($perms)) {
            $this->perms()->sync($perms); // происходит синхронизация связей
        }
        else {
            $this->perms()->detach(); // если к нам в качестве привилегии($perms) приходит пустой массив тогда мы отвязываем все привилегии от текущей роли
        }
    }

    // true
    // false
    public function hasPermission($alias, $require = false) { // Данный метод должен вернуть все привилегии конкретной роли; в alias передаем имя привилегии или массив имён привилегии;

//        dd($alias);
        if(is_array($alias)) {
            foreach ($alias as $permissionAlias) {
                $hasPermissions = $this->hasPermission($permissionAlias);
                if($hasPermissions && !$require) {
                    return true;
                }
                else if(!$hasPermissions && $require) { // если одна из привилегии не привязанная к роли
                    return false;
                }
            }
        }
        else {
            foreach ($this->perms as $permission) {
                if($permission->alias == $alias) {
                    return true;
                }
            }
        }

        return $require;


    }
}
