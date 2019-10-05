<?php
namespace App\Lib;

use Singsys\LQ\Lib\Concerns\NotificationTemplate;

class DatabaseNotificationCompiler
{
    use NotificationTemplate;

   /* protected $timeVeriables = ['appointment_enchance.start_datetime_in_utc' => 'd/m/Y h:i A'];
    protected $inTimeZone = 'UTC';
    protected $outTimeZone = 'UTC';*/

	public $templateName = '';
	public $data = [];

	function __construct($template_name, Array $data)
	{
		$this->timeVeriables = ['appointment_enchance.start_datetime_in_utc' => 'd/m/Y h:i A'];
        $this->data = $data;
        $this->templateName = $template_name;
        $request = app('request');
        $time_offset = $request->header('time-offset');
        $this->outTimeZone = $time_offset ? $time_offset : 'UTC';
	}

	public function get() {
		$template = $this->getTemaplate($this->templateName, $this->data);
		return $template['subject'];
	}

}
