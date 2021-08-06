<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use \App\Models\LiveAgentModel;

class BackendModel extends Model
{
    use HasFactory;

    protected $backendurl;
    protected $downstream;
    protected $message;
    protected $referenceid;
    
    function __construct($msisdn,$message,$channel) {

        $kbotcore_url = config('services.kbotcore.url');
        $handover_url = config('services.handover.url');

		$senderid = preg_replace( '/[^0-9]/', '', $msisdn);
		$this->message = $message;
		$this->referenceid = $channel;

		// Get liveagent flag
		$handover = new LiveAgentModel();
		$status = $handover->status($senderid);

		// set routing attributes
		if($status == true) {
			$this->downstream = "Handover";
			$endpoint = $handover_url."/inbound/customer/message";
		} else {
			$this->downstream = "KbotCore";
			$endpoint = $kbotcore_url."/inbound/";
		}

		$this->backendurl = $endpoint."?refrenceId=".$this->referenceid."&senderId=$senderid";
	}

	public function send() {
        // construct json object for core
        $appurl = config('app.url');
        $referenceid = $this->referenceid;
        $message = $this->message;
        $backendurl = $this->backendurl;
        $downstream = $this->downstream;

        $messageid = (string)uniqid();
        $header = ["Content-type" => "application/json"];
        $object = [
            'callbackUrl' => $appurl.'/telegram/',
            'data' => $message,
            'isGuest' => true,
            'msgId' => $messageid,
            'referral' => $messageid,
            'referenceId' => $referenceid
        ];

        // send to core
        \Log::channel('transaction')->debug("$downstream <- PATH " . $backendurl);
        \Log::channel('transaction')->debug("$downstream <- HEADER " . json_encode($header));
        \Log::channel('transaction')->debug("$downstream <- BODY " . json_encode($object));
        $response = Http::withHeaders($header)->post($backendurl, $object);
        \Log::channel('transaction')->debug("$downstream <- RESP " . $response->status() . " " . $response);
	}

}
