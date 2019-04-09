<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\FacebookUserMismatch;
use App\Exceptions\GoogleUserMismatch;
use App\Http\Controllers\Controller;
use App\Traits\RedirectsUsers;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use sammaye\Flash\Support\Flash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, RedirectsUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $facebook_user = Socialite::driver('facebook')->user();

        if (!$facebook_user->email) {
            Flash::error(__('Your Facebook account has no email, please add one'));
            return redirect()
                ->route('register');
        }

        $email_user = User::query()->where('email', $facebook_user->email)->first();
        $id_user = User::query()->where('facebook_id', $facebook_user->id)->first();

        if ($id_user && $email_user && !$email_user->is($id_user)) {
            throw new FacebookUserMismatch;
        }

        $user = User::updateOrCreate(
            ['email' => $facebook_user->email],
            [
                'username' => Str::slug($facebook_user->name, '_') . rand(100, 3234567),
                'facebook_id' => $facebook_user->id,

            ]
        );

        $this->guard()->login($user);
        return redirect()->intended($this->redirectPath());
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $google_user = Socialite::driver('google')->user();

        if (!$google_user->email) {
            Flash::error(__('Your Google account has no email, please add one'));
            return redirect()
                ->route('register');
        }

        if (!$google_user->user['email_verified']) {
            Flash::error(__('Your Google account email is not verified, please verify it'));
            return redirect()
                ->route('register');
        }

        $email_user = User::query()->where('email', $google_user->email)->first();
        $id_user = User::query()->where('google_id', $google_user->id)->first();

        if ($id_user && $email_user && !$email_user->is($id_user)) {
            throw new GoogleUserMismatch;
        }

        $user = User::updateOrCreate(
            ['email' => $google_user->email],
            [
                'username' => Str::slug($google_user->name, '_') . rand(100, 3234567),
                'google_id' => $google_user->id,

            ]
        );

        $this->guard()->login($user);
        return redirect()->intended($this->redirectPath());
    }
}
