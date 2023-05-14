<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return Response|Application|ResponseFactory
     * @throws ValidationException
     */
    public function updateProfile(Request $request): Response|Application|ResponseFactory
    {
        $user = $request->user();

        $this->validate($request, [
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'patronymic' => ['nullable', 'string'],
            'email' => ['sometimes', 'email', 'string'],
        ]);

        $user->update($request->all());

        if ($request->filled('email')) {
            event(new Registered($user));
        }

        return $this->response($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return array|Application|RedirectResponse|Response|Redirector
     * @throws ValidationException
     */
    public function updatePassword(Request $request): Response|array|Redirector|Application|RedirectResponse
    {
        $user = $request->user();

        $this->validate($request, [
            'old_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8', 'string'],
        ]);

        if ($user->password != Hash::make($request->input('old_password'))) {
            return $this->responseError('Ваш текущий пароль неправильный!');
        }

        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return $this->response($request->user());
    }
}
