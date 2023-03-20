<?php

namespace App\Modules\Admin\User\Requests;

use App\Services\Requests\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->canDo(['SUPER_ADMINISTRATOR','USERS_ACCESS']); // чтобы пользователь мог выполнить запрос, у него должно быть привилегия SUPER_ADMINISTRATOR или роль USERS_ACCESS

    }

    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance(); // берем валидатор который предоставляет нам Laravel

        $validator->sometimes('password',['required', 'confirmed'],function ($input) { // sometimes() - позволяет нам определить некий набор правил; $input - объект входящего запроса

            if(!empty($input->password) || (empty($input->password) && ($this->route()->getName() != 'api.users.update'))) {
                return true; // если callback функция вернет true тогда password будет внесён в правила валидации
            }
            return false;
        });

        return $validator;// Вернем валидатор Laravel чтобы ничего не сломалось в процессе валидации
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'firstname'=>'required',
            'lastname'=>'required',
            'email'=>'required',
            'telephone'=>'required',
            'role_id'=>'required',
        ];
    }
}
