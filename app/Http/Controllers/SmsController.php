<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{
    public function sendSms(Request $request)
    {
        $number = $request->input('number'); // e.g. '09171234567'
        $message = $request->input('message'); // e.g. 'Welcome to our app!'

        $payload = [
            'apikey'  => config('services.semaphore.api_key'),
            'number'  => $number,
            'message' => $message,
        ];

        $sender = config('services.semaphore.sender_name');

        if ($sender) {
            $payload['sendername'] = $sender;
        }

        $response = Http::asForm()->post('https://api.semaphore.co/api/v4/messages', $payload);

        if ($response->successful()) {
            return $response->json();
        } else {
            return [
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }
    }
}

