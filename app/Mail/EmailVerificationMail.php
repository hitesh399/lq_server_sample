<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Singsys\LQ\Lib\Concerns\NotificationTemplate;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels, NotificationTemplate;

    private $data = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Array $data)
    {
        //
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $template = $this->getTemaplate('EMAIL_VERFICATION_EMAIL', $this->data);
        return $this->html($template['body'])
            ->subject($template['subject']);
    }
}
