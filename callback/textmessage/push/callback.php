<?php

namespace kbotcore;

class callback
{
	public $packet;
	public $msisdn;
	public $message;

	public function __construct()
	{
		// receive packet from core
		$packet = trim(file_get_contents("php://input"));
		$this->packet= trim(preg_replace('/\s\s+/', '', $packet));
		$object = json_decode($this->packet, true);

		// get msisdn
		$recipientIds = $object['recipientIds'];
		foreach($recipientIds as $msisdn)
			break;
		if($msisdn[0] == '0')
			$msisdn = '6'.$msisdn;
		else if($msisdn[0] == '+')
			$msisdn = substr($msisdn,1);
		$this->msisdn = $msisdn;

		// for textmessage, use object message
		// for menumessage, use object subtitle
		$title = $object['title'];
		$subTitle = $object['subTitle'];
		$message= $object['message'];
		$this->message = "*$title*\n$message";
	}
}

?>
