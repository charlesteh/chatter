<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');


Route::auth();

Route::get('/home', 'HomeController@index')->name('dashboard');
Route::get('/conversation/{id}', 'ChatController@conversation')->name('conversation');

Route::get('fire', function () {

    // this fires the event

	$new_message = new App\Message;

    $new_message->user_id = 2;
    $new_message->recipient_id = 3;
    $new_message->message = 'Test fire';
    $new_message->save();

    event(new App\Events\MessageWasSent($new_message));
    return "MessageWasSent Event Fired";
});


Route::group(['middleware' => 'api','prefix' => 'api'], function () {

	Route::post('/sendMessage','ChatController@sendMessage')->name('conversation.send');
    Route::post('/checkMessage','BlacklistKeywordController@checkMessageContent')->name('message.check');

});

