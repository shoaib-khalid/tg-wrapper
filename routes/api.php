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
        Route::resource('incoming', 'IncomingController');
        Route::resource('callback/textmessage/push', 'PushTextMessageController');
        Route::resource('callback/menumessage/push', 'PushMenuMessageController');
        Route::resource('callback/conversation/handle', 'HandleConversationController');
        Route::resource('callback/conversation/pass', 'PassConversationController');
    }
);
