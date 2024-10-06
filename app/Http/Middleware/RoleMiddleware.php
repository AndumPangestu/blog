<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Periksa apakah user telah login
        if (!Auth::check()) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "Unauthorized"
                    ]
                ]
            ])->setStatusCode(401));
        }

        // Dapatkan user yang sedang login
        $user = Auth::user();

        // Periksa apakah user memiliki role yang diizinkan
        if ($user->role !== $role) {
            // Jika user tidak memiliki role yang diizinkan, kembalikan respon 403
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "Unauthorized action."
                    ]
                ]
            ])->setStatusCode(403));
        }

        // Lanjutkan permintaan jika user memiliki role yang benar
        return $next($request);
    }
}
