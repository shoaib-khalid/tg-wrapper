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

        \Log::channel('transaction')->debug("LOG Start Pass Conversation ------------------------------------------------");
        \Log::channel('transaction')->debug("Backend -> PATH " . config('app.url') . preg_replace('/[\r\n\t ]+/','',$request->getRequestUri()));
        \Log::channel('transaction')->debug("Backend -> HEADER", $request->header());
        \Log::channel('transaction')->debug("Backend -> BODY " . preg_replace('/[\r\n\t ]+/','',$request->getContent()));

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
            \Log::channel('transaction')->debug("LOG End Pass Conversation ------------------------------------------------");
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
            \Log::channel('transaction')->debug("LOG End Pass Conversation ------------------------------------------------");
            return response()->json(
                [
                    'status' => false,
                    'errors' => $description,
                ],
                400
            );
        }

        
        $userId=$request["recipientIds"][0];
        $referenceId=$request["referenceId"];
        
        // add msisdn to liveagent routing
        $handover = new LiveAgentModel();
        $handover->insert($userId);
        
        $msgToCS = "*Handover from Telegram*\nPlease greet the customer (eg: Hi, how may I assist you today?)";

        // determine routing
        $backend = new BackendModel($userId,$msgToCS,$referenceId);
        $backend->send();
        \Log::channel('transaction')->debug("LOG End Pass Conversation ------------------------------------------------");
    }
}
