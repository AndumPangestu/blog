<?php

namespace App\Listeners;

use App\Events\EmailChanged;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailChange implements ShouldQueue
{

    use InteractsWithQueue;
    /**
     * Handle the event.
     *
     * @param  \App\Events\EmailChanged  $event
     * @return void
     */
    public function handle(EmailChanged $event)
    {
        $user = $event->user;

        // Ubah status verifikasi email menjadi null
        $user->email_verified_at = null;
        $user->save();

        // Kirim ulang email verifikasi ke email baru
        $user->sendEmailVerificationNotification();

        Log::info("User email changed from {$event->oldEmail} to {$event->newEmail}. Verification email sent.");
    }
}
