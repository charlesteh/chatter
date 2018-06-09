<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\User;
use App\Message;
use Auth;
use App\Events\MessageWasSent;

class ChatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function conversation($id)
	{
		$chat_to_user = User::where('id',$id)->first();

        $messages_sender = Message::where('user_id',$chat_to_user->id)
                                    ->where('recipient_id',Auth::id())
                                    ->get();

         $messages_recipient = Message::where('recipient_id',$chat_to_user->id)
                                    ->where('user_id',Auth::id())
                                    ->get();

        //$messages_sender = Message::whereIn('user_id',[$chat_to_user->id, Auth::id()])->get();
        //$messages_recipient = Message::whereIn('recipient_id',[$chat_to_user->id, Auth::id()])->get();

        $messages = $messages_sender->merge($messages_recipient);
        $messages = $messages->unique();
        $messages = $messages->sortBy('created_at');

		return view('conversation',compact('chat_to_user','messages'));
	}

    public function sendMessage(Request $request)
    {
        
        $to = $request->recipient_id;
        $from = $request->sender_id;
        $message_content = $request->message;

        if (empty($message_content))
        {
            $data = [
                'status' => -1,
                'error' => 'Empty message content.',
            ];

            return response()->json($data);
        }

        // Create New Message
        $new_message = new Message;
        $new_message->user_id = $from;
        $new_message->recipient_id = $to;
        $new_message->message = $message_content;
        $new_message->save();

        // Fire MessageWasSent Event
        event(new MessageWasSent($new_message));

        $data = [
            'status' => 0,
            'message_id' => $new_message->id,
            'message_content' => $new_message->message,
            'sender_id' => $new_message->user_id,
            'recipient_id' => $new_message->recipient_id,
        ];

        return response()->json($data);
    }
}
