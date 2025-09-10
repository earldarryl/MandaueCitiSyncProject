<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
class CustomVerifyEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    protected function generateOTP($notifiable)
    {
        $otp = rand(100000, 999999);
        Cache::put('email_otp_' . $notifiable->email, $otp, now()->addMinutes(10));
        return $otp;
    }

    public function toMail($notifiable)
    {
        $otp = $this->generateOTP($notifiable);

        return (new MailMessage)
            ->subject('Your Email Verification OTP')
            ->line("Use the following One-Time Password (OTP) to verify your email:")
            ->line("**$otp**")
            ->line('This OTP is valid for 10 minutes.');
    }
}
