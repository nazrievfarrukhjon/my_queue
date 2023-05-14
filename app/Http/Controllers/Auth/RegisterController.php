<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected string $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param RegisterRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function register(RegisterRequest $request): Application|ResponseFactory|Response
    {
        event(new Registered($user = $this->create($request->all())));

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->guard()->login($user);

        return $this->response([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data): User
    {
        $user = (new User())->forceFill([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'patronymic' => $data['patronymic'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'service_center_id' => $data['service_center_id']
        ]);

        $user->save();

        return $user;
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
