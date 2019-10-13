<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class Send extends Mailable
{
    use Queueable;
    use SerializesModels;
    private $_attachments = [];

    /**
     * Create a new message instance.
     */
    public function __construct(string $templateName, array $data = [], array $timeVeriables = [], array $attachments = [])
    {
        $this->setTemplate($templateName, $data, $timeVeriables);
        $this->_attachments = $attachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $build = $this->html($this->template['body'])
            ->subject($this->template['subject']);

        if (count($this->_attachments)) {
            $_attachments = isset(
                $this->_attachments['name']
            ) ? [$this->_attachments] : $this->_attachments;

            foreach ($_attachments as $attachment) {
                $build->attachData(
                    $attachment['raw'],
                    $attachment['name'],
                    [
                        'mime' => $attachment['mime'],
                    ]
                );
            }
        }

        return $build;
    }
}
