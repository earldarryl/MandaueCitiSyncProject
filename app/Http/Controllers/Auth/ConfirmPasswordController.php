<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Http\Request;


class ConfirmPasswordController extends Controller
{


    public function __invoke(Request $request)
        {
            $request->session()->passwordConfirmed();

            return redirect()->intended(route('dashboard', absolute: false));
        }
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    // use ConfirmsPasswords;

    // /**
    //  * Where to redirect users when the intended url fails.
    //  *
    //  * @var string
    //  */
    // protected $redirectTo = '/dashboard';

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }


}
