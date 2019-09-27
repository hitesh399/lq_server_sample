<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Mail;

class SMSChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        try {
            $sms = $notification->toSms($notifiable);
            $sms->send();
        } catch (\Exception $e) {
            \Log::info('SMS'. $e->getMessage());
        }
    }
}
