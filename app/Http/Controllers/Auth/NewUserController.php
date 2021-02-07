<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use Carbon\Carbon;

class NewUserController extends Controller
{
    public function verify($link)
    {
        $user = User::where('remember_token', $link)->first();

        if ($user) {
            if ($user->email_verified_at == NULL) {
                $user->update(['email_verified_at' => Carbon::now()]);

                $token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($user);

                return view('auth.passwords.reset', ['user' => $user, 'token' => $token, 'email' => $user->email]);
            }
        }
        return redirect('login');
    }
}
