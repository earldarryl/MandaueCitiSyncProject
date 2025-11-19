<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{
    public function sendSms(Request $request)
    {
        $number = $request->input('number');
        $message = $request->input('message');

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

