<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \App\Models\LiveAgentModel;

class HandleConversationController extends Controller
{
    /**
     * Revoke chat conversation from handover service
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

        \Log::channel('transaction')->info("LOG Start Handle Conversation ------------------------------------------------");
        \Log::channel('transaction')->info("Backend -> PATH " . config('app.url') . preg_replace('/[\r\n\t ]+/','',$request->getRequestUri()));
        \Log::channel('transaction')->info("Backend -> HEADER", $request->header());
        \Log::channel('transaction')->info("Backend -> BODY " . preg_replace('/[\r\n\t ]+/','',$request->getContent()));

        $userId=$request["recipientIds"][0];

        // remove msisdn from liveagent routing
        $handover = new LiveAgentModel();
        $handover->remove($userId);
        \Log::channel('transaction')->info("LOG End Handle Conversation ------------------------------------------------");
    }
}
