<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AuthController extends Controller
{
    /**
     * API Login, on success return JWT Auth token
     *
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function login(Request $request): Application|ResponseFactory|Response
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return $this->responseError('We cant find an account with this credentials.', ResponseAlias::HTTP_UNAUTHORIZED);
            }
        } catch (Exception $e) {
            Log::critical($e->getMessage(), $e->getTrace());

            return $this->responseError('Failed to login, please try again.', ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

        $user = User::query()->where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->response(['token' => $token]);
    }

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     *
     * @param Request $request
     * @return Application|Response|ResponseFactory
     * @throws ValidationException
     */
    public function logout(Request $request): Application|ResponseFactory|Response
    {
        try {
            Auth::user()->tokens()->each(function ($query) {
                $query->delete();
            });
            return $this->response([], "You have successfully logged out.");
        } catch (Exception $e) {
            return $this->responseError('Failed to logout, please try again.', 500);
        }
    }

    /**
     * @param Request $request
     * @return Response|Application|ResponseFactory
     */
    public function me(Request $request): Response|Application|ResponseFactory
    {
        return $this->response(User::with(['roles', 'services', 'permissions'])->findOrFail($request->user()->id));
    }
}
