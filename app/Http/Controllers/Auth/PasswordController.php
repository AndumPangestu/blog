<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendResetLinkEmailRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PasswordController extends Controller
{
    public function sendResetLinkEmail(SendResetLinkEmailRequest $request): JsonResponse
    {

        $data = $request->validated();


        $status = Password::sendResetLink(
            ['email' => $data['email']]
        );

        if ($status !== Password::RESET_LINK_SENT) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        __($status)
                    ]
                ]
            ])->setStatusCode(422));
        }


        return response()->json([
            'message' => __($status)
        ], 200);
    }


    public function getToken($token, Request $request): JsonResponse
    {
        $data['token'] = $token;
        $data['email'] = $request->query('email');

        // Tampilkan atau gunakan data token dan email
        return response()->json([
            'data' => $data
        ]);
    }


    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {

        $data = $request->validated();


        $status = Password::reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
                'token' => $data['token'],
            ],
            function ($user, $password) {

                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();


                event(new PasswordReset($user));
            }
        );


        if ($status !== Password::PASSWORD_RESET) {

            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        __($status)
                    ]
                ]
            ])->setStatusCode(422));
        }

        return response()->json([
            'message' => __($status)
        ], 200);
    }
}
