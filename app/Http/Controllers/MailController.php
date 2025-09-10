<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendWelcome()
    {
        $recipientEmail = 'arnelamaba999@gmail.com';
        $name = 'John Doe';

        Mail::to($recipientEmail)->send(new WelcomeMail($name));

        return 'Welcome email sent to Gmail!';
    }
}
