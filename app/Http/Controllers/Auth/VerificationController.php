<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerificationController extends Controller
{
    /**
     * Where to redirect users after verification.
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
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request)
    {
        $user = User::query()->findOrFail($request->id);

        if ($user->hasVerifiedEmail()) {
            return redirect(config('app.front_url') . '/email/verified')->with('verified', true);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect(config('app.front_url') . '/email/verified')->with('verified', true);
    }

    /**
     * Resend the email verification notification.
     *
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function resend(Request $request): Application|ResponseFactory|Response
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->response([], "User has verify email.");
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->response();
    }
}
