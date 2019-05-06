<?php

namespace App\Listeners;

use App\Mail\WelcomeMail;
use Illuminate\Support\Str;
use App\Models\UserVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeMail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        // Send Welcome Email with Email verification Link
        $user = $event->user;
        $email_token = Str::random(6);
        $user_verifications = UserVerification::create([
            'email' => $user->email,
            'user_id' => $user->id,
            'token' => $email_token,
            'for'   =>  'email_verification'
        ]);
        $email_verification_link = env('APP_URL') . '/email/verification/' . $email_token;
        $notification_data = [
            'user' => $user->toArray(),
            'user_verifications' => $user_verifications,
            'link'  => $email_verification_link
        ];

        Mail::to($user)->send(new WelcomeMail($notification_data));
    }
}
