<?php

namespace App\Modules\Pub\Auth\Requests;

use App\Services\Requests\ApiRequest;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() // данный метод нужен для проверки прав и привилегии пользователя
    {

        return true; // вернем true если пользователю разрешается выполнение текущего запроса
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() // вернем массив правил валидации
    {
        return [
            //
            'email'=>'required',
            'password'=>'required',
        ];
    }
}
