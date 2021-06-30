<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class TelegramModel extends Model
{
    use HasFactory;

    protected $url;
    protected $token;
    protected $msisdn;
    protected $message;
    protected $replyMessage;

    function __construct($msisdn,$message,$replyMessage=[]) {
        $this->url =  config('services.telegram.url');
        $this->token =  config('services.telegram.url');
        $this->msisdn = $msisdn;
        $this->message = $message;
        $this->replyMessage = $replyMessage;
    }

    function send(){

        $url = $this->url;
        $token = $this->token;
        $msisdn = $this->msisdn;
        $message = $this->message;
        $replyMessage = $this->replyMessage;

        $endpoint = $url . "/bot" . $token . "/sendmessage";

        $data = [];
        if(count($replyMessage) > 0){
            // for menu messages
            $data = [
                'chat_id' => $msisdn,
                'text' => $message,
                'parse_mode' => 'markdown',
                'reply_markup' => json_encode($replyMessage,true)
            ];
        } else {
            // for text messages
            $data = [
                'chat_id' => $msisdn,
                'text' => $message,
                'parse_mode' => 'markdown',
            ];
        }
        $parameters = http_build_query($data);
        $header = ["Content-type: application/json"];
        $object = [];

        // send to core
        \Log::channel('transaction')->info("Telegram <- PATH " . $endpoint);
        \Log::channel('transaction')->info("Telegram <- PARAM " . $parameters);
        $response = Http::withHeaders($header)->get($endpoint . "?" . $parameters);
        \Log::channel('transaction')->info("Telegram <- RESP " . $response);
    }
}
