<?php

require_once "lphp/lcurl_v1.0/client.php";
include_once 'liveagent.php';

class router
{
	public $logHeader;
	public $logMessage;
	private $url;
	private $message;

	public function __construct($sourceNumber, $message)
	{
		$senderId = preg_replace( '/[^0-9]/', '', $sourceNumber);
		$this->message = $message;

		// Get liveagent flag
		$handover = new liveagent();
		$status = $handover->status($senderId);

		// set routing attributes
		$refId = "12019890471";
		if($status == TRUE)
		{
			$this->logHeader = "Send to RocketChat";
			$endpoint = "209.58.160.20:1058/inbound/customer/message";
		}
		else
		{
			$this->logHeader = "Send to KbotCore";
			$endpoint = "209.58.160.20:7313/inbound/";
		}

		$this->url = "$endpoint?refrenceId=$refId&senderId=$senderId";
	}

	public function send()
	{
		// set json object for core
		$msgId = (string)uniqid();
		$header = array("Content-type: application/json");

		$object = array();
		$object['callbackUrl'] = 'https://symplified.biz/kbot/telegram/';
		$object['data'] = $this->message; 
		$object['isGuest'] = true;
		$object['msgId'] = $msgId;
		$object['referral'] = $msgId;
		$httpBody = json_encode($object,JSON_UNESCAPED_SLASHES);

		// submit to core
		$h = new \lcurl\client($this->url);
		$h->setheader($header);
		$code = $h->post($httpBody);    

		// log response from core
		$log = "\n".$this->logHeader;
		$log.= "\n\tCall $this->url";
		$log.= "\n\tBody $httpBody";
		$log.= "\n\tReply ".$h->response();
		$this->logMessage = $log;
	}
}

?>
