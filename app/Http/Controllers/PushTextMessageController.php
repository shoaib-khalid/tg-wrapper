<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \App\Models\TelegramModel;

class PushTextMessageController extends Controller
{
    /**
     * Sent a single text message to telegram client
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        \Log::channel('transaction')->info("LOG Start Push TextMessage ------------------------------------------------");
        \Log::channel('transaction')->info("Backend -> PATH " . config('app.url') . preg_replace('/[\r\n\t ]+/','',$request->getRequestUri()));
        \Log::channel('transaction')->info("Backend -> HEADER", $request->header());
        \Log::channel('transaction')->info("Backend -> BODY " . preg_replace('/[\r\n\t ]+/','',$request->getContent()));

        $validate = Validator::make(
            $request->all(), [ 
                'recipientIds' => 'required|array',
                'title' => 'required|string',
                'subTitle' => 'nullable|string',
                'message' => 'required|string',
                'refId' => 'required|string',
                'referenceId' => 'required|string',
            ]);
            
            if ($validate->fails()) {
                return response()->json(
                [
                    'status' => false,
                    'errors' => $validate->errors(),
                ],
                400
            );
        }

        if (count($request["recipientIds"]) > 1) {
            return response()->json(
                [
                    'status' => false,
                    'errors' => "Multiple recipientIds detected. This API only accept only 1 recipientId",
                ],
                400
            );
        }


        $textmessage = "*" . $request["title"] . "*" . "\n" .
                    //    "`" . $request["subTitle"] . "`" . "\n\n" . 
                       $request["message"];

        // calling telegram
        $telegram = new TelegramModel($request["recipientIds"][0],$textmessage,$request["referenceId"]);
        $response = $telegram->send();
        \Log::channel('transaction')->info("LOG End Push TextMessage ------------------------------------------------");

        return $response;
    }
}
