<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \App\Models\BackendModel;
use \App\Models\LiveAgentModel;

class PassConversationController extends Controller
{
  /**
     * Pass chat conversation to handover service
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
                'message' => 'required|string',
                'refId' => 'required|string',
                'referenceId' => 'required|string',
            ]
        );

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

        \Log::channel('transaction')->info("Backend -> PATH " . config('app.url') . preg_replace('/[\r\n\t ]+/','',$request->getRequestUri()));
        \Log::channel('transaction')->info("Backend -> HEADER", $request->header());
        \Log::channel('transaction')->info("Backend -> BODY " . preg_replace('/[\r\n\t ]+/','',$request->getContent()));
        
        $userId=$request["recipientIds"][0];
        
        // add msisdn to liveagent routing
        $handover = new LiveAgentModel();
        $handover->insert($userId);
        
        $msgToCS = "*Handover from Telegram*\nPlease greet the customer (eg: Hi, how may I assist you today?)";

        // determine routing
        $backend = new BackendModel($userId,$msgToCS,"@SymplifiedBot");
        $backend->send();
    }
}
