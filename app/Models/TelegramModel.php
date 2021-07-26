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

    function __construct($msisdn,$message,$botusername) {
        $this->url = config('services.telegram.url');
        $this->botusername =  $botusername;
        $this->msisdn = $msisdn;
        $this->message = $message;
    }

    function send(){

        $url = $this->url;
        $botusername = $this->botusername;
        $msisdn = $this->msisdn;
        $message = $this->message;

        $response = $this->getUserServiceByRefId($botusername);
        if (gettype($response) == "array" && $response["status"] === false) {
            return response()->json($response,400);
        }
 
        $token = $response["data"]["content"][0]["token"];

        // get token based on $botusername
        $endpoint = $url . "/bot" . $token . "/sendmessage";

        $data = [];
        if(gettype($message) == "array"){
            // for menu messages
            $data = [
                'chat_id' => $msisdn,
                'text' => $message["textHeader"],
                'parse_mode' => 'markdown',
                'reply_markup' => json_encode($message["keyboard"],true)
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
        $header = ["Content-type" => "application/json"];
        $object = [];

        // send to telegram
        \Log::channel('transaction')->info("Telegram <- PATH " . $endpoint);
        \Log::channel('transaction')->info("Telegram <- HEADER " . json_encode($header));
        \Log::channel('transaction')->info("Telegram <- PARAM " . $parameters);
        $response = Http::withHeaders($header)->get($endpoint . "?" . $parameters);
        \Log::channel('transaction')->info("Telegram <- RESP " . $response);
    }

    private function getUserServiceByRefId($refId) {

        $url = config('services.userservice.url');
        $token = config('services.userservice.token');
        $endpoint = $url . "/userChannels";
        $data = [
            'refId' => $refId,
            'channelName' => 'telegram'
        ];
        $parameters = http_build_query($data);
        $header = [
            "Content-type" => "application/json",
            "Authorization" => "Bearer $token"
        ];
        
        // get token from user service
        \Log::channel('transaction')->info("User Service <- PATH " . $endpoint);
        \Log::channel('transaction')->info("User Service <- HEADER " . json_encode($header));
        \Log::channel('transaction')->info("User Service <- PARAM " . $parameters);
        $response = Http::withHeaders($header)->get($endpoint . "?" . $parameters);
        \Log::channel('transaction')->info("User Service <- RESP " . $response);

        if ($response["status"] !== 200) {
            $description = "User service give response !== 200";
            \Log::channel('transaction')->info("User Service <- ERROR " . $description);
            return [
                'system' => 'user-service',
                'action' => 'get userChannels',
                'status' => false,
                'system_response' => $response->json(),
                'description' => $description
            ];
        }

        if (empty($response["data"]["content"])){
            $description = "User service give response.data.content empty";
            \Log::channel('transaction')->info("User Service <- ERROR " . $description);
            return [
                'system' => 'user-service',
                'action' => 'get userChannels',
                'status' => false,
                'system_response' => $response->json(),
                'description' => $description
            ];
        }

        if (count($response["data"]["content"]) > 1){
            $description = "User service give response.data.content > 1";
            \Log::channel('transaction')->info("User Service <- ERROR " . $description);
            return [
                'system' => 'user-service',
                'action' => 'get userChannels',
                'status' => false,
                'system_response' => $response->json(),
                'description' => $description
            ];
        }

        return $response;
    }
}
