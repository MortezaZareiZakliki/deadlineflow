<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPasswordChangedEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user) {}

    public function handle(): void
    {
        Log::info("🔐 Security Alert: Password changed for user {$this->user->email}");
        
        // Mail::to($this->user->email)->send(new PasswordChangedMail($this->user));
    }
}