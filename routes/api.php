<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "telegram" middleware group. Enjoy building your API!
|
*/

Route::group(
    [
        'middleware' => 'telegram',
        'namespace'  => 'App\Http\Controllers',
    ],
    function ($router) {
        Route::post('incoming/', 'IncomingController@store');
        Route::post('callback/textmessage/push/', 'PushTextMessageController@store');
        Route::post('callback/menumessage/push/', 'PushMenuMessageController@store');
        Route::post('callback/conversation/handle/', 'HandleConversationController@store');
        Route::post('callback/conversation/pass/', 'PassConversationController@store');
    }
);

Route::fallback(function () {
    //Send to 404 or whatever here.
    return abort(404);
});
