<?php

namespace App\Modules\Admin\Role\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() // Данный метод выполняется когда, происходит запрос; Он проверяет если у пользователя есть права на выполнение текущего запроса
    {
        return Auth::user()->canDo(['SUPER_ADMINISTRATOR','ROLES_ACCESS']); // чтобы пользователь мог выполнить запрос, у него должно быть привилегия SUPER_ADMINISTRATOR или роль ROLES_ACCESS

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'title' => 'required',
            'alias' => 'required',
        ];
    }
}
