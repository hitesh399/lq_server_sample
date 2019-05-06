<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Mail;

class EmailChannel
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
        $mail = $notification->toEmail($notifiable);
        Mail::to($notifiable)->send($mail);
    }
}
