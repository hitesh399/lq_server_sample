<?php

namespace App\Mail;

use Illuminate\Mail\Mailable as BaseMailable;
use Singsys\LQ\Lib\Concerns\NotificationTemplate;

class Mailable extends BaseMailable
{
    use NotificationTemplate;
    protected $template = [];
    public $data = [];

    /**
     * Convert the given recipient into an object.
     *
     * @param mixed $recipient
     *
     * @return object
     */
    protected function normalizeRecipient($recipient)
    {
        if ($recipient instanceof \App\Models\User) {
            $this->data['user'] = $recipient->toArray();
            $this->outTimeZone = $recipient->timezone ? $recipient->timezone : 'UTC';
        }
        if (is_array($recipient)) {
            return (object) $recipient;
        } elseif (is_string($recipient)) {
            return (object) ['email' => $recipient];
        }

        return $recipient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    protected function setTemplate(string $name, array $data = [], array $timeVeriables = [])
    {
        $this->data = $data;
        $this->timeVeriables = $timeVeriables;
        $this->template = $this->getTemaplate($name, $this->data);
    }
}
