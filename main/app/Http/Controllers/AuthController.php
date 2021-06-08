<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Seller;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class AuthController.
 */
class AuthController extends Controller
{
    /**
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $domain = $request->getHost();

        $seller = Seller::whereDomain($domain)->first();

        if (!$seller) {
            return response()->json(['errors' => ['Unauthorized']], 401);
        }

        if ($seller->banned === 1) {
            return response()->json(['errors' => ['banned' => 'Вы были заблокированны.']], 401);
        }

        $sellerUsers = User::whereSellerId($seller->id)->get();

        $user = $sellerUsers->first(
            fn (User $user) => Hash::check($request->get('password'), $user->password)
        );

        if (!$user) {
            return response()->json(['errors' => ['Unauthorized']], 401);
        }

//        if ($user->role === User::ROLE_OPERATOR) {
//            $operator = $user->operator;
//            $shift = Shift::current()
//                ->where('seller_id', $user->seller_id)
//                ->first();
//        }

        $token = auth()->attempt([
            'email'    => $user->email,
            'password' => $request->get('password'),
        ]);

        if (!$token) {
            return response()->json(['errors' => ['Не верный пароль']], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function me(Request $request): \Illuminate\Http\JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if (!$user) {
            return response()->json(['errors' => ['Unauthorized']], 401);
        }

        $token = $request->header('Authorization', '');

        if (Str::startsWith($token, 'bearer ')) {
            $token = Str::substr($token, 7);
        }

        $user->token = $token;

        return response()->json($user);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): \Illuminate\Http\JsonResponse
    {
        auth()->logout();

        return response()->json([]);
    }

    /**
     * @return JsonResponse
     */
    public function refresh(): ?\Illuminate\Http\JsonResponse
    {
        try {
            $newToken = auth()->refresh();
            JWTAuth::setToken($newToken)->authenticate();

            return $this->respondWithToken($newToken);
        } catch (JWTException $e) {
            return response()->json(['errors' => ['Unauthorized']], 401);
        }
    }

    /**
     * @param $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token): \Illuminate\Http\JsonResponse
    {
        return response()->json(array_merge(Auth::user()->setHidden(['id', 'seller_id'])->toArray(), [
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]))
            ->header('Access-Control-Allow-Headers', 'Authorization')
            ->header('Access-Control-Expose-Headers', 'Authorization')
            ->header('Authorization', $token);
    }
}
