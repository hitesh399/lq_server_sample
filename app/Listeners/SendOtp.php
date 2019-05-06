<?php

namespace App\Listeners;

use App\Services\SMS;
use Illuminate\Support\Str;
use App\Models\UserVerification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOtp
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
        //
        // Send OTP If user Entered the Mobile Number.
        $user = $event->user;

        if ($user->mobile_no) {
            $otp = Str::random(6);
            $user_verifications = UserVerification::create([
                'email' => $user->email,
                'user_id' => $user->id,
                'token' => $otp,
                'for'   =>  'mobile_verification'
            ]);
            $notification_data = [
                'user' => $user->toArray(),
                'user_verifications' => $user_verifications
            ];

            $sms = new SMS($user->mobile_no, 'MOBILE_VERFICATION_SMS', $notification_data);
            $sms->send();
        }
    }
}
