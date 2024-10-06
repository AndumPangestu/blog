<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserRegistrationSuccess;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    // Kirim ulang email verifikasi
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 204);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent!']);
    }

    // Verifikasi email
    public function verify(Request $request): JsonResponse
    {
        $user = User::find($request->id);

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 204);
        }

        if ($user->markEmailAsVerified()) {
            event(new UserRegistrationSuccess($user));
        }

        return response()->json(['message' => 'Email has been verified.']);
    }
}
