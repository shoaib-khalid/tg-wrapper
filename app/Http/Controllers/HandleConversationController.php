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

        \Log::channel('transaction')->debug("LOG Start Handle Conversation ------------------------------------------------");
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
            \Log::channel('transaction')->debug("LOG End Handle Conversation ------------------------------------------------");
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
            \Log::channel('transaction')->debug("LOG End Handle Conversation ------------------------------------------------");
            return response()->json(
                [
                    'status' => false,
                    'errors' => $description,
                ],
                400
            );
        }


        $userId=$request["recipientIds"][0];

        // remove msisdn from liveagent routing
        $handover = new LiveAgentModel();
        $handover->remove($userId);
        \Log::channel('transaction')->debug("LOG End Handle Conversation ------------------------------------------------");
    }
}
