<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public $adminUser;

    /**
     * Guard used for admin user
     *
     * @var string
     */
    protected string $guard = 'admin';

    public function __construct()
    {
    }

    /**
     * Get logged user before each method
     *
     * @param Request $request
     */
    protected function setUser(Request $request)
    {
        if (empty($request->user($this->guard))) {
            abort(404, 'Admin User not found');
        }

        $this->adminUser = $request->user($this->guard);
    }

    /**
     * Show the form for editing logged user profile.
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function editProfile(Request $request): View|Factory|Application
    {
        $this->setUser($request);

        return view('admin.profile.edit-profile', [
            'adminUser' => $this->adminUser,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return array|Application|RedirectResponse|Redirector
     * @throws ValidationException
     */
    public function updateProfile(Request $request): array|Redirector|Application|RedirectResponse
    {
        $this->setUser($request);
        $adminUser = $this->adminUser;

        $this->validate($request, [
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($this->adminUser->getKey(), $this->adminUser->getKeyName()), 'string'],
            'language' => ['sometimes', 'string'],

        ]);

        $sanitized = $request->only([
            'first_name',
            'last_name',
            'email',
            'language',

        ]);

        $this->adminUser->update($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/profile'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/profile');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return Application|Factory|View|Response
     */
    public function editPassword(Request $request): View|Factory|Response|Application
    {
        $this->setUser($request);

        return view('admin.profile.edit-password', [
            'adminUser' => $this->adminUser,
        ]);
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
        $this->setUser($request);

        $this->validate($request, [
            'password' => ['sometimes', 'confirmed', 'min:7', 'string'],

        ]);

        $sanitized = $request->only([
            'password',

        ]);

        $sanitized['password'] = Hash::make($sanitized['password']);

        $this->adminUser->update($sanitized);

        if ($request->ajax()) {
            return ['redirect' => url('admin/password'), 'message' => trans('brackets/admin-ui::admin.operation.succeeded')];
        }

        return redirect('admin/password');
    }
}
