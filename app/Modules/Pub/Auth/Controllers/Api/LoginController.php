<?php

namespace App\Modules\Pub\Auth\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Pub\Auth\Requests\LoginRequest;
use App\Services\Response\ResponseServise;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    //

    public function login(LoginRequest $request) { // указываем что для валидации будем использовать правила из LoginRequest

        $credentials = request(['email','password']); // Данные из объекта запроса

        if(!Auth::attempt($credentials)) { // если авторизация не удалась
            return ResponseServise::sendJsonResponse( // метод, который вернет в качестве ответа JSON строку
                false,
                403,
                ['message' => __('auth.login_error')] // в качестве ответа
            );
        }

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token'); // createToken()- создает токен под конкретного пользователя, аргументом указываем имя данного токена

        return ResponseServise::sendJsonResponse(
            true,
            200,
            [],
            [ // формируем данные которые вернем в качестве ответа
                'api_token' => $tokenResult->accessToken,
                'user' => $user,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(), // время жизни токена
            ]
        );
    }
}
