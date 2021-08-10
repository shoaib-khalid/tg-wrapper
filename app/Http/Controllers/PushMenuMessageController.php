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

        $reqinfo = [
            "METHOD" => "PushMenuMessage",
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
                'menuItems' => 'required|array',
                'menuItems.*.type' => 'required|string',
                'menuItems.*.title' => 'required|string',
                'menuItems.*.payload' => 'required|integer',
                'refId' => 'nullable|string',
                'referenceId' => 'required|string',
            ]);

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

        // generating menu options
        $textHeader = "*" . $request["title"] . "*" . "\n" . $request["subTitle"];

        $menu = [];
        $menuItems = $request["menuItems"];

        foreach($menuItems as $menuItem) {
            $payload = $menuItem['payload'];
            $no = (int)$payload;

            $option = [];
            $unicodeNo = $no . ". " . $menuItem['title'];
            $option = [[
                "text" => $unicodeNo,
                "callback_data" => $no
            ]];
            array_push($menu,$option);        
        }

        $keyboard = [
            // 'inline_keyboard' => array($menu)
            // 'inline_keyboard' => array(
            //     [
            //         [
            //             "text" => "Test",
            //             "callback_data" => 1
            //         ],
            //         [
            //             "text" => "Test",
            //             "callback_data" => 1
            //         ]
            //     ]
            // )
            'inline_keyboard' => $menu
            // 'inline_keyboard' => array(
            //     [
            //         [
            //             "text" => "Test",
            //             "callback_data" => 1
            //         ]
            //     ],
            //     [
            //         [
            //             "text" => "Test",
            //             "callback_data" => 1
            //         ]
            //     ]
            // )
        ];

        $message = [
            "textHeader" => $textHeader,
            "keyboard" => $keyboard
        ];

        // calling telegram
        $telegram = new TelegramModel($request["recipientIds"][0],$message,$request["referenceId"]);
        $telegram->send();
        \Log::channel('transaction')->debug("LOG End " . $reqinfo["METHOD"] . " ------------------------------------------------");
    }
}
