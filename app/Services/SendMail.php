<?php

namespace App\Services;

use Mail;
use App\Mail\Send;

trait SendMail
{
    /**
     * To Send Email.
     *
     * @param string $templateName  Template Name
     * @param array  $data          Template Data
     * @param array  $timeVeriables Template time veriables ['time' => 'd/M/Y h:i A]
     * @param array  $attachments   Attachment Array format ['name' => 'name.pdf', 'raw' => '', 'mime' => 'application/pdf']
     *
     * @return Send
     */
    public function sendMail(string $templateName, array $data = [], array $timeVeriables = [], array $attachments = [])
    {
        return Mail::to($this)->send(
            new Send($templateName, $data, $timeVeriables, $attachments)
        );
    }
}
