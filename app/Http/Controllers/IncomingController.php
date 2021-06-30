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
    public function store(Request $request) {

        $validator = Validator::make(
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

        // capture only if both fails
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'errors' => $validator->errors(),
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
            $message = $request["message"]["text"];
        } else {
            return response()->json(
                [
                    'status' => false,
                    'errors' => "Either callback query field or message field can't be null",
                ],
                400
            );
        }

        \Log::channel('transaction')->info("Telegram -> PATH " . config('app.url') . preg_replace('/[\r\n\t ]+/','',$request->getRequestUri()));
        \Log::channel('transaction')->info("Telegram -> HEADER", $request->header());
        \Log::channel('transaction')->info("Telegram -> BODY " . preg_replace('/[\r\n\t ]+/','',$request->getContent()));

        // determine routing
        $backend = new BackendModel($userId,$message,"@SymplifiedBot");
        $backend->send();

    }//end store()


}
