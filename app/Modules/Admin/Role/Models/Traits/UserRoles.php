<?php
/**
 * Created by PhpStorm.
 * User: note
 * Date: 20.12.2020
 * Time: 13:25
 */

namespace App\Modules\Admin\Role\Models\Traits;


use App\Modules\Admin\Role\Models\Role;
use Illuminate\Support\Str;

trait UserRoles // Этот трейт хранит вспомогательную логику которая необходима для связи модуля User и Модуля Ролей и Привилегии;
{
    public function roles() { // У пользователя может быть много ролей
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function canDo($alias, $require = false) { // Вернем true или false если у пользователя есть соответсвующее привилегии; $alias - псевдоним привилегии;
        if(is_array($alias)) {
            foreach ($alias as $permName) {
                $result = $this->canDo($permName);
                if($result && !$require) { // если у пользователя есть привилегии
                    return true;
                }
                elseif(!$result && $require) {
                    return false;
                }
            }
        }
        else {
            foreach ($this->roles as $role) {
                foreach ($role->perms as $perm) { // Привилегии определенной роли
                    if(Str::is($alias, $perm->alias)) { // Если $alias равен $perm->alias
                        return true;
                    }
                }
            }
        }

        return $require;

    }

    public function hasRole($alias, $require = false) { //Проверяем наличие Роли у пользователя; $alias - псевдоним роли
        if (is_array($alias)) {
            foreach ($alias as $roleName) {
                $hasRole = $this->hasRole($roleName);

                if ($hasRole && !$require) {
                    return true;
                } elseif (!$hasRole && $require) {
                    return false;
                }
            }
            return $require;
        } else {
            foreach ($this->roles as $role) {
                if ($role->alias == $alias) {
                    return true;
                }
            }
        }

        return $require;
    }

    public function getMergedPermissions() { // Возвращаем для пользователя массив всех привилегии которые привязаны к роли пользователя
        $result = [];
        foreach ($this->getRoles() as $role) {
            $result = array_merge($result, $role->perms->toArray());
        }

        return $result;
    }

    public function getRoles() { // Возвращаем набор ролей пользователя
        if($this->roles) {
            return $this->roles;
        }

        return [];
    }

}
