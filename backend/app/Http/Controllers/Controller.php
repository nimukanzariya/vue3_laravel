<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Mix;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public static function response(String $status = null, mixed $data = null, String $message = "Data Found", array $other_data = []): mixed
    {
        $response = [
            "status" => $status,
            "message" => $message,
            "data" => $data
        ];
        $response = array_merge($response, $other_data);
        return response()->json($response);
    }
}
