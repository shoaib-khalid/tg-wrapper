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

        $reqinfo = [
            "METHOD" => "PushTextMessage",
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
                'recipientIds' => 'required|array',
                'title' => 'required|string',
                'subTitle' => 'required|string',
                'message' => 'required|string',
                'refId' => 'nullable|string',
                'referenceId' => 'required|string',
            ]
        );
            
        if ($validate->fails()) {
            \Log::channel('transaction')->debug("Backend <- RESP " . $validate->errors());
            \Log::channel('transaction')->debug("LOG End " . $reqinfo["METHOD"] . " ------------------------------------------------");
            return response()->json(
                [
                    'status' => false,
                    'errors' => $validate->errors(),
                ],
                400
            );
        }

        if (count($request["recipientIds"]) > 1) {
            $description = "Multiple recipientIds detected. This API only accept only 1 recipientId";
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


        $textmessage = "*" . $request["title"] . "*" . "\n" .
                    //    "`" . $request["subTitle"] . "`" . "\n\n" . 
                       $request["message"];

        // calling telegram
        $telegram = new TelegramModel($request["recipientIds"][0],$textmessage,$request["referenceId"]);
        $telegram->send();
        \Log::channel('transaction')->debug("LOG End " . $reqinfo["METHOD"] . " ------------------------------------------------");
    }
}
