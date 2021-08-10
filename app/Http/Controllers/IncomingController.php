<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \App\Models\BackendModel;

class IncomingController extends Controller
{
    
    /**
     * Receive a newly incoming request from telegram client
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $botid) {

        $reqinfo = [
            "METHOD" => "Incoming",
            "PATH" => config('app.url') . $request->getRequestUri(),
            "HEADER" => $request->header(),
            "BODY" => $request->getContent()
        ];

        \Log::channel('csv')->info("Receive " . $reqinfo["METHOD"] . " Request",$reqinfo);

        \Log::channel('transaction')->debug("LOG Start " . $reqinfo["METHOD"] . " ------------------------------------------------");
        \Log::channel('transaction')->debug("Backend -> PATH " . $reqinfo["PATH"]);
        \Log::channel('transaction')->debug("Backend -> HEADER", $reqinfo["HEADER"]);
        \Log::channel('transaction')->debug("Backend -> BODY " . preg_replace('/[\r\n\t ]+/',' ',$reqinfo["BODY"]));

        $validate = Validator::make(
            $request->all(), [ 
                'callback_query' => 'prohibited_unless:message,null|array',
                'callback_query.message.chat.id'  => 'prohibited_unless:message,null|integer',
                'callback_query.message.from.username'  => 'prohibited_unless:message,null|string',
                'callback_query.message.data'  => 'prohibited_unless:message,null|string',
        
                'message' => 'prohibited_unless:callback_query,null|array',
                'message.chat.id'  => 'prohibited_unless:callback_query,null|integer',
                'message.from.username'  => 'prohibited_unless:callback_query,null|string',
                'message.data'  => 'prohibited_unless:callback_query,null|string',
            ]
        );
        
        if ($validate->fails()) {
            \Log::channel('transaction')->debug("Telegram <- RESP " . $validate->errors());
            \Log::channel('transaction')->debug("LOG End " . $reqinfo["METHOD"] . " ------------------------------------------------");
            return response()->json(
                [
                    'status' => false,
                    'errors' => $validate->errors(),
                ],
                400
            );
        }

        // receive from telegram
        if (isset($request['callback_query'])){
            $userId = $request["callback_query"]["message"]["chat"]["id"];
            $username = "@".$request["callback_query"]["message"]["from"]["username"]; // will be use as reference id
            $message = $request["callback_query"]["data"];
        } else if (isset($request['message'])) {
            $userId = $request["message"]["chat"]["id"];
            $username = "@".$request["message"]["from"]["username"]; // will be use as reference id
            if (isset($request["message"]["text"])) {
                $message = $request["message"]["text"];
            } else {
                $message = "Non text messages sent by telegram client";
            }
        } else {
            $description = "Either callback query field or message field can't be null";
            \Log::channel('transaction')->debug("Backend <- RESP " . $description);
            \Log::channel('transaction')->debug("LOG End " . $reqinfo["METHOD"] . " ------------------------------------------------");
            return response()->json(
                [
                    'status' => false,
                    'errors' => $description,
                ],
                400
            );
        }

        // determine routing
        $backend = new BackendModel($userId,$message,$botid);
        $backend->send();
        \Log::channel('transaction')->debug("LOG End " . $reqinfo["METHOD"] . " ------------------------------------------------");
    }//end store()

}
