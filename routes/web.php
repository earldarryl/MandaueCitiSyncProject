<?php
use App\Http\Controllers\UserController;
use App\Livewire\Dashboard;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use App\Http\Controllers\SmsController;

require __DIR__.'/auth.php';


