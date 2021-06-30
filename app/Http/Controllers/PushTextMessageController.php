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

        if ($request["refId"] !== $request["recipientIds"][0]) {
            return response()->json(
                [
                    'status' => false,
                    'errors' => "refId need to match recipientId. refId[".$request["refId"]."] != recipientId[".$request['recipientIds'][0]."]",
                ],
                400
            );
        }

        $textmessage = "*" . $request["title"] . "*" . "\n" .
                    //    "`" . $request["subTitle"] . "`" . "\n\n" . 
                       $request["message"];

        // calling telegram
        $telegram = new TelegramModel($request["recipientIds"][0],$textmessage);
        $telegram->send();

    }
}
