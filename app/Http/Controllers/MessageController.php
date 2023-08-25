<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\SendMessage;

class MessageController extends Controller
{
    public function index(){
        return view('chat');
    }

    public function getmessage()
    {  
        return Message::with('user')->get();
    }


    // public function messageStore(Request $request){
    //     $message = auth()->user()->messages()->create([
    //         'message' => $request->message
    //     ]);
    //      broadcast(new SendMessage($message->load('user')));
    //      return ['status' => 'success'];

    // }

    public function messageStore(Request $request){
        $user = Auth::user();
        $message = $user->messages()->create([
            // 'userId' => $request->user['id'], 
            'message' => $request->message
        ]);
        // event(new TestEvent('This is a test message'));
        broadcast(new SendMessage($user, $message))->toOthers();
    
        return 'message sent';
    }

}
