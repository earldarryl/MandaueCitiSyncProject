<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class SendTestMail extends Command
{
    protected $signature = 'mail:test';
    protected $description = 'Send test welcome mail';

    public function handle(): void
    {
        Mail::to('test@example.com')->send(new WelcomeMail('Earl'));
        $this->info('Test mail sent!');
    }
}
