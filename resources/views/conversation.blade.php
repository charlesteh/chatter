@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-sm-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard')}}">Home</a></li>
                <li>Chat with {{ $chat_to_user->name}}</li>
            </ol>

            <h3>Hello, {{ Auth::user()->name }}. You're chatting with {{ $chat_to_user->name }}.</h3>
            <p>Type a message and press <b>Send</b>.</p>


            <div class="panel panel-default">
                <div class="panel-heading">
                    Chat with {{ $chat_to_user->name }}
                </div>
                <div class="panel-body pre-scrollable chat-box" id="chatBoxScrollable">
                    <div class="row" id="chatBoxContent">


                        @foreach ($messages as $message)
                        <div class="col-xs-12">

                            @if ($message->user_id === $chat_to_user->id)
                            <div class="sentence-left">
                                {{$message->message}}
                            </div>

                            @elseif ($message->user_id === Auth::id())
                            <div class="sentence-right">
                                {{$message->message}}
                            </div>
                            @endif

                            
                        </div>
                        @endforeach

                        {{--
                        <div class="col-xs-12">
                            <div class="sentence-left">
                                Hey, how are you?
                            </div>
                        </div>


                        <div class="col-xs-12">
                            <div class="sentence-right">
                                Nothing much, what about you?
                            </div>
                        </div>
                        --}}



                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="form-group">
                                <input type="text" name="message-content" id="messageContent" class="form-control" placeholder="Type a message">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <button id="messageSend" class="btn btn-success form-control">
                                    Send to {{ $chat_to_user->name}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <span class="text-warning" id="notifyUserText"></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('additional_css')
<style>

.sentence-left {
    float: left;
    background-color: white;
    color: black;
    padding: 5px;
    border-radius: 3px;
    -webkit-box-shadow: 0px 1px 1px 0px rgba(107,107,107,1);
    -moz-box-shadow: 0px 1px 1px 0px rgba(107,107,107,1);
    box-shadow: 0px 1px 1px 0px rgba(107,107,107,1);
    margin-bottom: 10px;


}

.sentence-right {
    float: right;
    background-color: #dafbd6;
    color: black;
    padding: 5px;
    border-radius: 3px;
    -webkit-box-shadow: 0px 1px 1px 0px rgba(107,107,107,1);
    -moz-box-shadow: 0px 1px 1px 0px rgba(107,107,107,1);
    box-shadow: 0px 1px 1px 0px rgba(107,107,107,1);
    margin-bottom: 10px;
}

.chat-box {
    height: 250px;
    max-height: 250px;
    background-color: #efefef;
    padding: 20px;
}
</style>
@endsection

@section('external_js')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>
@endsection

@section('additional_js')
<script>
    /*
     * Message Send Handler
     */

    var SEND_MESSAGE_BUTTON = '#messageSend';
    var SEND_MESSAGE_CONTENT = '#messageContent';
    var SEND_MESSAGE_SENDER = {{ Auth::id() }};
    var SEND_MESSAGE_RECIPIENT = {{ $chat_to_user->id }};
    var SEND_MESSAGE_API = '{{ route('conversation.send') }}';
    var CHECK_MESSAGE_API = '{{ route('message.check') }}';
    var CHAT_BOX_CONTENT = '#chatBoxContent';
    var CHAT_BOX_SCROLLALBE = '#chatBoxScrollable';
    var NOTIFY_USER_TEXT = '#notifyUserText';


    $(document).ready(function(){

        // Attach Event Listeners
        $(SEND_MESSAGE_BUTTON).click(function() {
            sendMessage();
        });

        resetChatUI();

        $(document).keypress(function(event){

            var keycode = (event.keyCode ? event.keyCode : event.which);

            // Check if is Enter key
            if(keycode == '13'){
                sendMessage();  
            }
        });

        // Scroll Chatbox to lowest
        scrollChatBoxToBottom();

    });

    function resetChatUI() {
        $(NOTIFY_USER_TEXT).text(' ');
    }

    function scrollChatBoxToBottom() {
        var sHeight = $(CHAT_BOX_SCROLLALBE)[0].scrollHeight;
        $(CHAT_BOX_SCROLLALBE).scrollTop(sHeight);
    }

    function sendMessage() {

        // Reset Chat UI
        resetChatUI();

        // Check if message input is empty
        if ($(SEND_MESSAGE_CONTENT).val().length === 0) {
            $(SEND_MESSAGE_CONTENT).focus();

        // if not empty then do the following:
        } else {

            var message_clean = false;

            // Check message for profanity
            axios.post(CHECK_MESSAGE_API,{
                'message': $(SEND_MESSAGE_CONTENT).val(),
            })
            .then(function (response) {
                console.log(response.data);

                if (response.data.status >= 0) {
                    message_clean = true;
                }
                else if (response.data.status < 0) {
                    message_clean = false;

                    // Notify the user
                    $(NOTIFY_USER_TEXT).text(response.data.error);
                }
            })
            .then (function() {

                if (message_clean) {

                    console.log('message is clean');

                    // Send message to server
                    axios.post(SEND_MESSAGE_API, {
                        'sender_id': SEND_MESSAGE_SENDER,
                        'recipient_id': SEND_MESSAGE_RECIPIENT,
                        'message': $(SEND_MESSAGE_CONTENT).val(),
                    })
                    .then(function (response) {
                        console.log(response);

                        if (response.data.status >= 0) {

                            // Clear Chat Input
                            $(SEND_MESSAGE_CONTENT).val('');
                            $(SEND_MESSAGE_CONTENT).focus();

                            // Update UI
                            var newMessage = '<div class="col-xs-12">' +
                                '<div class="sentence-right">' +
                                    response.data.message_content +
                                '</div>' +
                                '</div>'

                            //$(CHAT_BOX_CONTENT).append(newMessage);

                            // Scroll chat box content to lowest
                            scrollChatBoxToBottom();

                        } else {

                        }
                    })
                    .catch (function (error) {
                        console.log(error);
                    })
                }
            })
        }
    }
</script>

<script>

    var SEND_MESSAGE_SENDER = {{ Auth::id() }};
    var SEND_MESSAGE_RECIPIENT = {{ $chat_to_user->id }};

    /*
     * Socket.IO Handler
     */
    var uri = '{{url('/')}}';
    var port = 3000;
    var socket = io( uri + ':' + port );
    socket.on("message-channel:App\\Events\\MessageWasSent", function(broadcast) {
        
        // Update message when broadcast is recieved

        // Update UI
        var newMessage = '<div class="col-xs-12">';

        // Determine if is by sender or recipient
        if ((broadcast.message.recipient_id == SEND_MESSAGE_SENDER) && (broadcast.message.user_id == SEND_MESSAGE_RECIPIENT))
        {
            var newMessage = '<div class="col-xs-12">' +
                                '<div class="sentence-left">' +
                                    broadcast.message.message +
                                '</div>' +
                            '</div>'

            // Push new message
            $(CHAT_BOX_CONTENT).append(newMessage);

            // Scroll to bottom
            scrollChatBoxToBottom();
        }
        else if ((broadcast.message.recipient_id == SEND_MESSAGE_RECIPIENT) && (broadcast.message.user_id == SEND_MESSAGE_SENDER)) {
            var newMessage = '<div class="col-xs-12">' +
                                '<div class="sentence-right">' +
                                    broadcast.message.message +
                                '</div>' +
                            '</div>'

            // Push new message
            $(CHAT_BOX_CONTENT).append(newMessage);

            // Scroll to bottom
            scrollChatBoxToBottom();
        }


        
    });


</script>
@endsection