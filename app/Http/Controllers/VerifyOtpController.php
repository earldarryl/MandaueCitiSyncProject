<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request as RequestFacade;



class VerifyOtpController extends Controller
{

public function verifyOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|numeric',
    ]);

    $cachedOtp = Cache::get('email_otp_' . $request->user()->email);

    if ($cachedOtp && $cachedOtp == $request->input('otp')) {

        $request->user()->markEmailAsVerified();

        $roleName = ucfirst(auth()->user()->roles->first()?->name ?? 'user');

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'role_id'    => auth()->user()->roles->first()?->id,
            'action'     => $roleName . ' logged in',
            'ip_address' => RequestFacade::ip(),
            'device_info'=> RequestFacade::header('User-Agent'),
        ]);


        return redirect()->route('dashboard')->with('success', 'Email verified successfully.');
    }

    return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
}

}
