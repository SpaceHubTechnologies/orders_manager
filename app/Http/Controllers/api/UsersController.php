<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Transformers\Json;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Serializer\ArraySerializer;

class UsersController extends Controller
{

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     * user login api
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        if (Auth::attempt($data)) {
            $user = $request->user();

            $includes = [];

            $response = [
                'error' => false,
                'message' => 'Login successful',
                'user' => fractal()
                    ->item($user, new UserTransformer())
                    ->parseIncludes($includes)
                    ->serializeWith(new ArraySerializer())
            ];

            return response()->json($response, 200, [], JSON_PRETTY_PRINT);
        }

        return response()->json(Json::response(true, 'Invalid email and password'), 400);
    }



}
