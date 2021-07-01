<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \App\Models\TelegramModel;

class PushMenuMessageController extends Controller
{
    /**
     * Send Menu Messages to telegram client
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
        $validate = Validator::make(
            $request->all(), [ 
                'recipientIds' => 'required|array',
                'title' => 'required|string',
                'subTitle' => 'required|string',
                'menuItems' => 'required|array',
                'menuItems.*.type' => 'required|string',
                'menuItems.*.title' => 'required|string',
                'menuItems.*.payload' => 'required|integer',
                'refId' => 'required|string',
                'referenceId' => 'required|string',
            ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 400);
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

        // generating menu options
        $textHeader = "*" . $request["title"] . "*" . "\n" . $request["subTitle"];

        $menu = [];
        $menuItems = $request["menuItems"];

        foreach($menuItems as $menuItem) {
            $payload = $menuItem['payload'];
            $no = (int)$payload;

            $option = [];
            $unicodeNo = $no . ". " . $menuItem['title'];
            $option = [
                "text" => $unicodeNo,
                "callback_data" => $no
            ];
            array_push($menu,$option);        
        }

        $keyboard = [
            'inline_keyboard' => array($menu)
        ];

        // calling telegram
        $telegram = new TelegramModel($request["recipientIds"][0],$textHeader,$keyboard);
        $telegram->send();

    }
}
