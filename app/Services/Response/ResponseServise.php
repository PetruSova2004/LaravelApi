<?php
/**
 * Created by PhpStorm.
 * User: note
 * Date: 15.11.2020
 * Time: 10:22
 */

namespace App\Services\Response;


class ResponseServise
{
    private static function responsePrams($status, $errors = [], $data = []) { // формируем параметры запроса
        return [
            'status' => $status,
            'errors' => (object)$errors,
            'data' => (object)$data,
        ];
    }

    public static function sendJsonResponse($status, $code = 200, $errors = [], $data = []) { // отправляем в качестве ответа JSON строку
        return response()->json(
            self::responsePrams($status, $errors, $data),
            $code
        );
    }

    public static function success($data = []) { // этот метод мы будем использовать для отправки положительного ответа с определенном набором данных
        return self::sendJsonResponse(true, 200, [],$data);
    }

    public static function notFound($data = []) {
        return self::sendJsonResponse(false, 404, [],[]);
    }
}
