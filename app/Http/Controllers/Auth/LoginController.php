<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(\Illuminate\Http\Request $request)
    {
        $login = $request->input($this->username());

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } else {
            $user = \App\Models\User::whereRaw('LOWER(name) = ?', [strtolower($login)])
                ->orWhereRaw('LOWER(employee_id) = ?', [strtolower($login)])
                ->first();

            if ($user && strtolower($user->employee_id) === strtolower($login)) {
                $field = 'employee_id';
                $login = $user->employee_id;
            } elseif ($user) {
                $field = 'name';
                $login = $user->name;
            } else {
                $field = 'name';
            }
        }

        return [
            $field => $login,
            'password' => $request->input('password'),
        ];
    }
}
