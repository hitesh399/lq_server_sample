<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $data = [];

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
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
