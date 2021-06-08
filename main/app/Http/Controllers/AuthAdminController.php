<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\AdminLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthController.
 */
class AuthAdminController extends Controller
{
    /**
     * @param AdminLoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(AdminLoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        $user = User::whereRole(User::ROLE_ADMIN)
            ->where('email', $request->get('email'))
            ->first();

        $token = auth()->attempt($credentials);

        if (!$user || !$token) {
            return response()->json(['errors' => ['Unauthorized'], 'message' => 'Неверные логин или пароль', 'test'=>$credentials], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @param $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user'       => Auth::user(),
        ])
            ->header('Access-Control-Allow-Headers', 'Authorization')
            ->header('Access-Control-Expose-Headers', 'Authorization')
            ->header('Authorization', $token);
    }
}
