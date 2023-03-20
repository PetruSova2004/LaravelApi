<?php

namespace App\Modules\Pub\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    //
    use AuthenticatesUsers; // тут присутствует метод login() для авторизации пользователя

    protected $redirectTo = '/admin/users'; // Путь куда будет перенаправляться залогиненый пользователь

    public function  __construct() // запретим вход сюда для аутентифицированных пользователей; Для пользователей которые хотят выйти из учетной записи разрешим доступ
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        $title = __('Login');

        return view('Pub::Auth.login'); // ModularProvider строка ~ 62
    }

}
