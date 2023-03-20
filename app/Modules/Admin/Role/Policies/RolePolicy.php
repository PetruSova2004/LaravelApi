<?php

namespace App\Modules\Admin\Role\Policies;

use App\Modules\Admin\User\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function view(User $user) {
        return $user->canDo(['SUPER_ADMINISTRATOR','ROLES_ACCESS']); // чтобы пользователь мог выполнить действие view(), у него должно быть привилегия SUPER_ADMINISTRATOR или ROLES_ACCESS
    }

    public function create(User $user) {
        return $user->canDo(['SUPER_ADMINISTRATOR','ROLES_ACCESS']);
    }

    public function edit(User $user) {
        return $user->canDo(['SUPER_ADMINISTRATOR','ROLES_ACCESS']);
    }

    public function delete(User $user) {
        return $user->canDo(['SUPER_ADMINISTRATOR','ROLES_ACCESS']);
    }
}
