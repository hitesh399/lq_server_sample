<?php

namespace App\Services;

use Singsys\LQ\Lib\Concerns\NotificationTemplate;

class Sms
{
    use NotificationTemplate;

    protected $twilioSms;
    protected $accountId = '212323';
    protected $token = '212323';
    protected $fromNumber = '212323';

    protected $template_name = null;
    protected $mobile_no = null;
    protected $data = [];

    public function __construct($mobile_no, $template_name, $data)
    {

        /**
         * Set the Twilio credentials
         */
        $this->fromNumber = app('site_config')->get('TWILIO_SENDER_ID');
        $this->token = app('site_config')->get('TWILIO_TOKEN');
        $this->accountId = app('site_config')->get('TWILIO_SID');

        $this->twilioSms = new \Aloha\Twilio\Twilio($this->accountId, $this->token, $this->fromNumber);
        $this->template_name = $template_name;
        $this->mobile_no = $mobile_no;
        $this->data = $data;
    }

    /**
     * To send the sms.
     */
    public function send()
    {
        $sms_text = $this->getTemaplate($this->template_name, $this->data);
        return $this->twilioSms->message($this->mobile_no, $sms_text['body']);
    }
}
